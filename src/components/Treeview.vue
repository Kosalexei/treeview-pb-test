<template>
  <div class="treeview">
    <table class="treeview__table">
      <thead class="treeview__table-head">
        <tr>
          <th v-for="header in headers" :key="header.value">{{header.label}}</th>
        </tr>
      </thead>
      <tbody class="treeview__table-body">
        <tr v-for="(item, rIndex) in items" :key="rIndex">
          <td v-for="(header, dIndex) in headers" :key="dIndex">{{header.value ? item[header.value] : null}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {},

  data: () => ({
    headers: [
      {
        label: "Наименование",
        value: "name"
      },
      {
        label: "Тип",
        value: "type"
      },
      {
        label: "Дата создания",
        value: "created"
      },
      {
        label: "Дата изменения",
        value: "modified"
      }
    ],

    items: [
      {
        name: "Test",
        type: "Папка с файлами",
        created: "14.10.2019",
        modified: "14.10.2019"
      },

      {
        name: "Test",
        type: "Файл",
        created: "14.10.2019",
        modified: "14.10.2019"
      },

      {
        name: "Test",
        type: "Директория",
        created: "14.10.2019",
        modified: "14.10.2019"
      },

      {
        name: "Test",
        type: "Директория",
        created: "14.10.2019",
        modified: "14.10.2019"
      }
    ]
  }),

  methods: {}
};
</script>