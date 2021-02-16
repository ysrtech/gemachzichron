<template>
  <button
    :class="[colorClasses, {'opacity-25': processing}]"
    :disabled="processing || disabled"
    :type="type"
    class="inline-flex items-center px-4 py-2 border rounded-md shadow-sm hover:shadow-md focus:shadow-lg focus:outline-none  font-semibold text-xs uppercase transition ease-in-out duration-150">

    <div v-if="processing" class="spinner mr-2"/>

    <slot/>
  </button>
</template>

<script>
export default {
  name: 'AppButton',

  props: {
    type: {
      type: String,
      default: 'button',
    },
    color: {
      type: String,
      default: 'primary'
    },
    processing: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    colorClasses() {
      return {
        'primary': 'bg-primary-700 border-transparent text-white hover:bg-primary-800 focus:bg-primary-900',
        'secondary': 'bg-white border-gray-200 text-gray-500 hover:text-gray-700 focus:text-gray-700',
        'danger': 'bg-red-600 border-transparent text-white hover:bg-red-700 focus:bg-red-800',
      }[this.color]
    },
  }
}
</script>

<style scoped>
.spinner, .spinner:after {
  border-radius: 50%;
  width: 1.5em;
  height: 1.5em;
}

.spinner {
  font-size: 10px;
  position: relative;
  text-indent: -9999em;
  border-top: .2em solid white;
  border-right: .2em solid white;
  border-bottom: .2em solid white;
  border-left: .2em solid transparent;
  transform: translateZ(0);
  animation: spinning 1s infinite linear;
}

@keyframes spinning {
  0% {
    transform: rotate(0deg)
  }
  100% {
    transform: rotate(360deg)
  }
}
</style>
