<template>
  <modal
    v-model="dialog"
    :title="title"
  >
    <template v-slot:body>
      <form
        @submit.prevent="done()"
        class="form"
      >
        <div class="form-group">
          <label for="element-name">Название</label>
          <input
            id="element-name"
            v-model="elementName"
          />
        </div>

        <div class="form-group">
          <label for="element-type">Тип</label>
          <select v-model="elementType">
            <option
              v-for="(type, index) in types"
              :key="index"
              :value="type.ID"
            >{{type.Name}}</option>
          </select>
        </div>
      </form>
    </template>
    <template v-slot:action>
      <button
        class="btn btn-primary"
        @click="done()"
      >Добавить</button>
    </template>
  </modal>
</template>

<script>
import modal from "./mixin";

export default {
  mixins: [modal],

  props: {
    types: {
      type: Array,
      default: () => []
    }
  },

  data: () => ({
    elementName: null,
    elementType: 1
  })
};
</script>