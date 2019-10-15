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
          <label for="dir-name">Название</label>
          <input
            id="dir-name"
            v-model="dirName"
          />
        </div>

        <div class="form-group">
          <label for="dir-description">Описание</label>
          <textarea
            id="dir-description"
            v-model="dirDescription"
          ></textarea>
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

  data: () => ({
    dirName: null,
    dirDescription: null
  }),

  watch: {
    dialog(value) {
      if (!value) {
        this.dirName = null;
        this.dirDescription = null;
      }
    }
  }
};
</script>