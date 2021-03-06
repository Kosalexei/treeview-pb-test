<template>
  <div class="treeview">
    <div class="treeview__actions">
      <button
        class="treeview__action btn btn-primary"
        @click="addDirectoryModal = true"
      >Добавить директорию</button>
      <button
        class="treeview__action btn btn-primary"
        @click="addElementModal = true"
        v-if="parseInt(dirID) !== 0"
      >Добавить элемент</button>
      <button
        class="treeview__action btn btn-warning"
        @click="editModal = true"
        v-if="selectedID.length === 1"
      >Редактировать</button>

      <button
        class="treeview__action btn btn-danger"
        v-if="selectedID.length > 0"
        @click="deleteItems(dirID)"
      >Удалить</button>
    </div>

    <table class="treeview__table">
      <thead class="treeview__table-head">
        <tr>
          <th
            v-for="header in headers"
            :key="header.value"
            @click="setSort(header.value)"
            :class="{'sort-by': sortBy === header.value}"
          >
            <span>
              {{header.label}}
              <i
                class="icon icon--left mdi"
                :class="[sortBy === header.value ? (order === 'DESC' ? 'mdi-menu-down' : 'mdi-menu-up') : '']"
              ></i>
            </span>
          </th>
        </tr>
      </thead>

      <tbody class="treeview__table-body">
        <tr class="comeback" v-if="parseInt(dirID) !== 0" @dblclick="getDirectory(parentID)">
          <td :colspan="headers.length">
            <div>
              <i class="icon icon--left mdi mdi-arrow-left"></i>
              <span>...</span>
            </div>
          </td>
        </tr>

        <tr
          v-for="(directory, rIndex) in directories"
          :key="`${rIndex}-directory`"
          @dblclick="!ctrlPressed ? getDirectory(directory.ID, directory.ParentID) : () => {}"
          @click="selectRow(directory.advancedId)"
          :class="{'selected': selectedID.includes(directory.advancedId), 'want-move': wantMove.includes(directory.advancedId)}"
        >
          <td v-for="(header, dIndex) in headers" :key="dIndex">
            <span v-if="header.date">{{header.value ? moment(directory[header.value]) : null}}</span>
            <div class="td-name" v-else-if="header.value === 'Name'">
              <i class="icon icon--left mdi" :class="getIcon(directory.Type.ID)"></i>
              <span>{{header.value ? directory[header.value] : null}}</span>
            </div>
            <span
              v-else-if="header.value === 'Type'"
            >{{header.value ? directory[header.value].Name : null}}</span>
            <span v-else>{{header.value ? directory[header.value] : null}}</span>
          </td>
        </tr>

        <tr
          v-for="(element, rIndex) in elements"
          :key="`${rIndex}-element`"
          @click="selectRow(element.advancedId)"
          :class="{'selected': selectedID.includes(element.advancedId), 'want-move': wantMove.includes(element.advancedId)}"
        >
          <td v-for="(header, dIndex) in headers" :key="dIndex">
            <span v-if="header.date">{{header.value ? moment(element[header.value]) : null}}</span>
            <div class="td-name" v-else-if="header.value === 'Name'">
              <i class="icon icon--left mdi" :class="getIcon(element.Type)"></i>
              <span>{{header.value ? element[header.value] : null}}</span>
            </div>
            <span
              v-else-if="header.value === 'Type'"
            >{{getType(element[header.value]) ? getType(element[header.value]).Name : null}}</span>
            <span v-else>{{header.value ? element[header.value] : null}}</span>
          </td>
        </tr>
      </tbody>
    </table>

    <add-directory-modal
      title="Добавить директорию"
      v-model="addDirectoryModal"
      @done="addDirectory($event)"
    ></add-directory-modal>

    <add-element-modal
      title="Добавить элемент"
      v-model="addElementModal"
      :types="types"
      @done="addElement($event)"
    ></add-element-modal>

    <edit-modal
      title="Редактировать"
      v-if="selectedID.length === 1"
      v-model="editModal"
      :types="types"
      :item="notObservableFirstSelected"
      @done="updateItem($event)"
    ></edit-modal>
  </div>
</template>

<script>
import AddDirectoryModal from "./Modals/AddDirectoryModal";
import AddElementModal from "./Modals/AddElementModal";
import EditModal from "./Modals/EditModal";
import { find, xor } from "lodash";
import moment from "moment";

export default {
  components: { AddDirectoryModal, AddElementModal, EditModal },

  data: () => ({
    /**
     * Отображать ли модальное окно создания директории.
     */
    addDirectoryModal: false,

    /**
     * Отображать ли модальное окно создания элемнта.
     */
    addElementModal: false,

    /**
     * Отображать ли модальное окно редактирования объектов.
     */
    editModal: false,

    /**
     * Идентификатор текущей директории.
     */
    dirID: 0,

    /**
     * Идентификатор родительской директории.
     */
    parentID: 0,

    /**
     * Массив идентификаторов выбранных объектов.
     */
    selectedID: [],

    /**
     * Массив идентификаторов объектов, которые будут перемещены.
     */
    wantMove: [],

    /**
     * Идентификатор директории откуда перемещаем объекты.
     */
    moveFrom: null,

    /**
     * Определение, что клавиша CTRL нажата.
     */
    ctrlPressed: false,

    /**
     * По какому полю сортировать.
     */
    sortBy: null,

    /**
     * Порядок сортировки.
     */
    order: "DESC",

    /**
     * Заголовки таблицы.
     */
    headers: [
      {
        label: "Наименование",
        value: "Name"
      },
      {
        label: "Тип",
        value: "Type"
      },
      {
        label: "Дата создания",
        value: "Created",
        date: true
      },
      {
        label: "Дата изменения",
        value: "Modified",
        date: true
      }
    ],

    /**
     * Массив дочерних директорий.
     */
    directories: [],

    /**
     * Массив дочерних элементов.
     */
    elements: [],

    /**
     * Массив типов записей.
     */
    types: []
  }),

  computed: {
    /**
     * Объект без реактивности для внесения изменений.
     */
    notObservableFirstSelected() {
      if (this.selectedID.length > 0) {
        const idData = this.selectedID[0].split("-");
        const collection =
          idData[1] === "element" ? this.elements : this.directories;
        const obj = find(
          collection,
          item => item.advancedId === this.selectedID[0]
        );

        return JSON.parse(JSON.stringify(obj));
      }
      return null;
    }
  },

  watch: {
    /**
     * Следим открыто ли модальное окно добавления директории.
     */
    addDirectoryModal(value) {
      if (value) {
        // Убираем события.
        this.unsetEvents();
      } else {
        // Добавляем события.
        this.setEvents();
      }
    },

    /**
     * Следим открыто ли модальное окно добавления элемента.
     */
    addElementModal(value) {
      if (value && this.types.length === 0) {
        // Получаем типы записей
        this.getTypes();
      }

      if (value) {
        // Убираем события.
        this.unsetEvents();
      } else {
        // Добавляем события.
        this.setEvents();
      }
    },

    /**
     * Следим открыто ли модальное окно редактирования объекта.
     */
    editModal(value) {
      if (value && this.types.length === 0) {
        // Получаем типы записей
        this.getTypes();
      }

      if (value) {
        // Убираем события.
        this.unsetEvents();
      } else {
        // Добавляем события.
        this.setEvents();
      }
    }
  },

  mounted() {
    // Получаем директории
    this.getDirectory(this.dirID);

    // Получаем типы записей
    this.getTypes();

    // Добавляем события.
    this.setEvents();
  },

  beforeDestroy() {
    // Убираем события.
    this.unsetEvents();
  },

  methods: {
    /**
     * Добавление директории.
     */
    addDirectory({ dirName, dirDescription }) {
      const data = {
        dir_name: dirName,
        parent_id: this.dirID,
        dir_description: dirDescription
      };

      this.$http({ method: "post", url: "/directory", data })
        .then(({ data }) => {
          if (!data.data) return;
          const _data = data.data;
          this.updateData(_data);
          this.addDirectoryModal = false;
        })
        .catch(e => {
          console.log(e);
        });
    },

    /**
     * Получение дочерних элементов.
     */
    getDirectory(id, prarentID) {
      const params = { parent_id: id };
      this.parentID = prarentID;

      this.$http({ method: "get", url: "/directory", params })
        .then(({ data }) => {
          if (!data.data) return;
          const _data = data.data;
          this.selectedID = [];
          this.dirID = _data.dirId;
          this.updateData(_data);
        })
        .catch(e => {
          console.log(e);
        });
    },

    /**
     * Удаление объектов.
     */
    deleteItems(parent_id) {
      // Получаем идентификаторы выбранных объектов. Отдельно директории и элементы.
      const idsAdvanced = this.parseIdsAdvanced(this.selectedID);
      const data = { ids: idsAdvanced, parent_id };

      this.$http({
        method: "delete",
        url: "/directory",
        data
      })
        .then(({ data }) => {
          if (!data.data) return;
          const _data = data.data;
          this.selectedID = [];
          this.updateData(_data);
        })
        .catch(e => {
          console.log(e);
        });
    },

    /**
     * Добавить элемент.
     */
    addElement({ elementName, elementType }) {
      const data = {
        element_name: elementName,
        dir_id: parseInt(this.dirID),
        element_type: elementType
      };

      this.$http({ method: "post", url: "/element", data })
        .then(({ data }) => {
          if (!data.data) return;
          const _data = data.data;
          this.updateData(_data);
          this.addElementModal = false;
        })
        .catch(e => {
          console.log(e);
        });
    },

    /**
     * Обновить объект.
     */
    updateItem(item) {
      const data = {
        ids: [item.ID],
        currentDirID: item.ParentID || item.DirectoryID,
        Name: item.Name,
        Type:
          typeof item.Type !== "object" &&
          typeof parseInt(item.Type) === "number"
            ? item.Type
            : null,
        target: item.advancedId.split("-")[1],
        fields: ["Name", "Type"],
        modified: true
      };

      this.$http({ method: "update", url: "/directory", data })
        .then(({ data }) => {
          if (!data.data) return;
          const _data = data.data;
          this.updateData(_data);
          this.editModal = false;
        })
        .catch(e => {
          console.log(e);
        });
    },

    /**
     * Перемещение объектов.
     */
    moveItems(fromId, ids, toId, target) {
      const data = {
        ids: ids,
        currentDirID: toId,
        ParentID: toId,
        DirectoryID: toId,
        target: target,
        fields: ["ParentID", "DirectoryID"],
        modified: false
      };

      this.$http({ method: "update", url: "/directory", data })
        .then(({ data }) => {
          if (!data.data) return;
          const _data = data.data;
          this.wantMove = [];
          this.updateData(_data);
        })
        .catch(e => {
          console.log(e);
        });
    },

    /**
     * Перемещение и директорий и элементов.
     */
    moveAll() {
      if (this.wantMove.length === 0 || this.moveFrom === null) return;

      if (this.moveFrom === this.dirID) {
        this.wantMove = [];
        return;
      }

      // Получаем идентификаторы объектов, которые можно переместить. Отдельно директории и элементы.
      const idsAdvanced = this.parseIdsAdvanced(this.wantMove);

      for (let target in idsAdvanced) {
        this.moveItems(this.moveFrom, idsAdvanced[target], this.dirID, target);
      }
    },

    /**
     * Получаем типы записей.
     */
    getTypes() {
      this.$http({ method: "get", url: "/types" })
        .then(({ data }) => {
          if (!data.data) return;
          const _data = data.data;

          this.types = _data.items;
        })
        .catch(e => {
          console.log(e);
        });
    },

    /**
     * Выбираем строку из таблицы при клике на нее.
     */
    selectRow(id) {
      if (!this.ctrlPressed) this.selectedID = [id];
      else {
        if (this.selectedID.includes(id)) {
          const index = this.selectedID.indexOf(id);

          this.selectedID.splice(index, 1);
        } else {
          this.selectedID.push(id);
        }
      }
    },

    /**
     * Добавлем тип объектам, у которых его нет.
     */
    fixTypes(items) {
      items.forEach(item => {
        if (!item.hasOwnProperty("Type")) {
          item.Type = { ID: -1, Name: "Папка с файлами" };
        }
      });
    },

    /**
     * Получить тип объекта.
     */
    getType(id) {
      return find(this.types, type => type.ID === id);
    },

    /**
     * Получить иконку объекта.
     */
    getIcon(id) {
      let icon = "";

      switch (parseInt(id)) {
        case 1:
          icon = "mdi-newspaper color--purple";
          break;
        case 2:
          icon = "mdi-post-outline color--grey";
          break;
        case 3:
          icon = "mdi-message-text-outline color--orange";
          break;
        case 4:
          icon = "mdi-comment-text-multiple-outline color--blue";
          break;
        default:
          icon = "mdi-folder-outline color--yellow";
      }

      return icon;
    },

    /**
     * Задает дополнительный идентификатор. Основной может дублироваться между директориями и элементами.
     * И так легче отличить директорию от элемента.
     */
    addAdvancedIds(items, postfix) {
      items.forEach(item => {
        if (!item.hasOwnProperty("advancedId")) {
          item.advancedId = `${item.ID}-${postfix}`;
        }
      });
    },

    /**
     * Обновление данных после взаимодействия с сервером.
     */
    updateData(data) {
      this.directories = data.directories;
      this.elements = data.elements;
      this.addAdvancedIds(this.directories, "directory");
      this.addAdvancedIds(this.elements, "element");
      this.fixTypes(this.directories);
      this.sortAll();
    },

    /**
     * Задает порядок сортировки.
     */
    setSort(value) {
      if (this.sortBy === value) {
        this.order = this.order === "DESC" ? "ASC" : "DESC";
      } else {
        this.sortBy = value;
        this.order = "DESC";
      }

      this.sortAll();
    },

    /**
     * Сортирует все данные.
     */
    sortAll() {
      if (this.sortBy) {
        this.sortItems(this.directories, this.sortBy, this.order);
        this.sortItems(this.elements, this.sortBy, this.order);
      }
    },

    /**
     * Сортирует определенные данные.
     */
    sortItems(items, sortBy, order) {
      items.sort((a, b) => {
        // Удаляем реактивность
        a = JSON.parse(JSON.stringify(a));
        b = JSON.parse(JSON.stringify(b));

        if (typeof a[sortBy] === "string" && typeof b[sortBy] === "string") {
          a[sortBy] = a[sortBy].toUpperCase();
          b[sortBy] = b[sortBy].toUpperCase();
        }

        if (a[sortBy] < b[sortBy]) {
          return order === "ASC" ? -1 : 1;
        }

        if (a[sortBy] > b[sortBy]) {
          return order === "ASC" ? 1 : -1;
        }

        return 0;
      });
    },

    /**
     * Добавляем идентификаторы объектов, которые можно будет перенести.
     */
    addWantMove(items) {
      if (this.moveFrom === null) {
        this.moveFrom = this.dirID;
      }

      if (this.moveFrom !== this.dirID) {
        this.wantMove = [];
        this.moveFrom = this.dirID;
      }

      // Ищем различия в массивах.
      const xorItems = xor(this.wantMove.concat(items), this.wantMove);

      this.wantMove = this.wantMove.concat(xorItems);
    },

    parseIdsAdvanced(ids) {
      let idsAdvanced = {};

      ids.forEach(id => {
        const idData = id.split("-");

        if (idData.length === 2) {
          const _id = idData[0];
          const _type = idData[1];

          if (!idsAdvanced.hasOwnProperty(_type)) {
            idsAdvanced[_type] = [_id];
          } else {
            idsAdvanced[_type].push(_id);
          }
        }
      });

      return idsAdvanced;
    },

    /**
     * Форматируем дату.
     */
    moment(value, format = "DD.MM.YYYY HH:mm") {
      return moment(value, "YYYY-MM-DD HH:mm").format(format);
    },

    /**
     * Добавляем события.
     */
    setEvents() {
      document.onkeydown = e => {
        if (e.ctrlKey) {
          this.ctrlPressed = e.ctrlKey;

          if (e.keyCode === 88) {
            // CTRL+X
            e.preventDefault();
            this.addWantMove(this.selectedID);
          }

          if (e.keyCode === 86) {
            // CTRL+V
            e.preventDefault();
            this.moveAll();
          }
        }

        if (e.keyCode === 27) {
          this.selectedID = [];
          this.wantMove = [];
          this.moveFrom = null;
        }
      };

      document.onkeyup = e => {
        if (!e.ctrlKey) this.ctrlPressed = false;
      };

      document.onmousemove = e => {
        this.ctrlPressed = e.ctrlKey;
      };
    },

    /**
     * Удаляем события.
     */
    unsetEvents() {
      document.onkeydown = undefined;
      document.onkeyup = undefined;
      document.onmousemove = undefined;
    }
  }
};
</script>