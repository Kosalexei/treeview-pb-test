<template>
  <div class="treeview">
    <div class="treeview__actions">
      <button @click="addDirectoryModal = true">Добавить директорию</button>
    </div>

    <table class="treeview__table">
      <thead class="treeview__table-head">
      <tr>
        <th v-for="header in headers" :key="header.value">{{header.label}}</th>
      </tr>
      </thead>
      <tbody class="treeview__table-body">
      <tr v-if="parseInt(dirId) !== 0" @click="getDirectory(parentID)">
        <td>...</td>
      </tr>
      <tr v-for="(item, rIndex) in items" :key="rIndex" @click="getDirectory(item.ID, item.ParentID)">
        <td v-for="(header, dIndex) in headers" :key="dIndex">{{header.value ? item[header.value] : null}}</td>
      </tr>
      </tbody>
    </table>

    <modal v-model="addDirectoryModal" title="Добавить директорию">
      <template v-slot:body>
        <form @submit.prevent="addDirectory()">
          <label for="dir-name"></label>
          <input id="dir-name" v-model="dirName">
        </form>
      </template>
      <template v-slot:action>
        <button @click="addDirectory()">Добавить</button>
      </template>
    </modal>
  </div>
</template>

<script>
    import Modal from "./Modal";

    export default {
        components: {Modal},

        props: {},

        data: () => ({
            addDirectoryModal: false,

            dirId: 0,

            parentID: 0,

            dirName: "",

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
                    value: "Created"
                },
                {
                    label: "Дата изменения",
                    value: "Modified"
                }
            ],

            items: []
        }),

        mounted() {
            this.getDirectory(this.dirId);
        },

        methods: {
            addDirectory() {
                const data = {dir_name: this.dirName, parent_id: this.dirId, dir_description: "Desc"};

                this.$http({method: "post", url: "/directory", data}).then(({data}) => {
                    if (!data.data) return;
                    const _data = data.data;
                    this.items = _data.items;
                    this.dirId = _data.dirId;
                    this.addDirectoryModal = false;
                    this.dirName = "";
                }).catch(e => {
                    console.log(e)
                })
            },

            getDirectory(id, prarentID) {
                const params = {parent_id: id};
                this.parentID = prarentID;

                this.$http({method: "get", url: "/directory", params}).then(({data}) => {
                    if (!data.data) return;
                    const _data = data.data;
                    this.dirId = _data.dirId;
                    this.items = _data.items;

                }).catch(e => {
                    console.log(e)
                })
            }
        }
    };
</script>