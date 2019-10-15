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
    dirName: null
  })
};
</script>