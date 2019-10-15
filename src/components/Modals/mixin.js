import Modal from "@/components/Modal";

export default {
  components: { Modal },

  props: {
    title: {
      type: String,
      default: "Модальное окно"
    },

    value: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    dialog: {
      get() {
        return this.value;
      },

      set(value) {
        this.$emit("input", value);
      }
    }
  },

  methods: {
    done(data) {
      this.$emit("done", data || this.$data);
    }
  }
};
