<template>
  <div v-if="message" class="fixed bottom-4 right-6 shadow-lg z-50">
    <transition leave-active-class="transition ease-in duration-500" leave-class="opacity-100"
                leave-to-class="opacity-0">
      <div class="flex justify-between bg-gray-800 text-white px-6 py-3 rounded items-center">
        <span>{{ message }}</span>
        <button class="ml-8 -mr-2 focus:outline-none flex items-center" type="button"
                @click.prevent="$page.props.flash.snackbar = null">
          <i
            class="material-icons p-2 -m-2 text-xl text-gray-400 hover:text-gray-200 rounded-full hover:bg-gray-600 focus:bg-gray-600">clear</i>
        </button>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: "Snackbar",

  computed: {
    message() {
      return this.$page.props.flash.snackbar
    },
  },

  watch: {
    message(val) {
      if (val) {
        setTimeout(() => this.$page.props.flash.snackbar = null, 3500)
      }
    }
  }
}
</script>
