<template>
  <teleport to="body">
    <transition
      leave-active-class="transition ease-in duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-if="show" class="fixed bottom-4 inset-x-10 sm:left-auto sm:right-6 shadow-lg z-50">
        <div class="flex justify-between bg-gray-800 text-white px-6 py-3 rounded items-center">
          <span>{{ message }}</span>
          <button class="ml-8 -mr-2 focus:outline-none flex items-center" type="button" @click="$emit('close')">
            <i class="material-icons-outlined p-1 -m-1 text-gray-400 hover:text-gray-200 rounded-full hover:bg-gray-600 focus:bg-gray-600">clear</i>
          </button>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script>

export default {
  name: "AppSnackbar",

  emits: ['close'],

  props: {
    show: Boolean,
    message: String,
  },
}
</script>
