<template>
  <modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
    <div>

      <div class="px-6 py-4 text-xl font-medium">
        <slot name="title">
        </slot>
      </div>

      <form @submit.prevent="$emit('submitted')">

        <div class="px-6 pt-4 pb-5">
          <slot name="form"></slot>
        </div>

        <div class="px-6 py-4 bg-gray-100 flex items-center justify-end">
          <slot name="footer"/>
        </div>

      </form>
    </div>


  </modal>
</template>

<script>
import Modal from "./Modal";

export default {
  components: {
    Modal,
  },

  props: {
    show: {
      default: false
    },
    maxWidth: {
      default: '2xl'
    },
    closeable: {
      default: true
    },
  },

  methods: {
    close() {
      this.$emit('close')
    },
  }
}
</script>
