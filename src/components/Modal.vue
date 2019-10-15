<template>
  <transition name="modal">
    <div class="modal-mask" v-if="dialog">
      <div class="modal-wrapper">
        <div class="modal-container">

          <div class="modal-header">
            <h3 class="modal-title">{{title}}</h3>
          </div>

          <div class="modal-body">
            <slot name="body">

            </slot>
          </div>

          <div class="modal-footer">
            <slot name="action">

            </slot>


              <button class="btn btn-danger" @click="dialog = false">
                Закрыть
              </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
    export default {
        name: "Modal",

        props: {
            value: {
                type: Boolean,
                default: false
            },

            title: {
                type: String,
                default: "Модальное окно"
            }
        },

        computed: {
            dialog: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit("input", value)
                }
            }
        }

    }
</script>