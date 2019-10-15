<template>
  <div id="app">
    <treeview />

    <div class="note">
      <div class="note__title">Заметка</div>

      <div class="note__content">
        <div class="note__row">В корневую директорию нельзя вставить элемент. :)</div>
        <div class="note__row">Чтобы перейти в следующую директорию дважды кликните по ней.</div>
        <div class="note__row">Чтобы вернуться на директорию выше дважды кликните по троеточию.</div>
        <div class="note__row">Сортировать можно по любому столбцу. Нажмите на заголовок столбца, чтобы изменить порядок.</div>
        <div class="note__row">Зажмите клавишу CTRL для выбора нескольких элементов.</div>
      </div>
    </div>
  </div>
</template>

<script>
import Treeview from "./components/Treeview.vue";

export default {
  name: "app",
  components: {
    Treeview
  }
};
</script>
