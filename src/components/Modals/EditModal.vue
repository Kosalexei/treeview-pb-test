<template>
  <modal
    v-model="dialog"
    :title="title"
  >
    <template v-slot:body>
      <form
        @submit.prevent="done(localItem)"
        class="form"
        v-if="localItem"
      >
        <div class="form-group">
          <label for="element-name">Название</label>
          <input
            id="element-name"
            v-model="localItem.Name"
          />
        </div>

        <div
          class="form-group"
          v-if="localItem.Type.ID !== -1"
        >
          <label for="element-type">Тип</label>
          <select v-model="localItem.Type">
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
        @click="done(localItem)"
      >Обновить</button>
    </template>
  </modal>
</template>

<script>
import modal from "./mixin";

export default {
  mixins: [modal],

  props: {
    item: {
      type: Object,
      default: () => {}
    },

    types: {
      type: Array,
      default: () => []
    }
  },

  data: () => ({
    localItem: null
  }),

  watch: {
    dialog(value) {
      if (value) {
        this.localItem = JSON.parse(JSON.stringify(this.item));
      }
    }
  }
};
</script>