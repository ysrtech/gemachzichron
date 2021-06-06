<template>
  <span
    class="inline-flex items-center font-medium rounded-full"
    :class="[colorClasses, sizeClasses, caseClasses]">
    <slot/>
  </span>
</template>

<script>
export default {
  name: 'AppBadge',

  props: {
    color: {
      type: String,
      default: 'primary',
    },
    size: {
      type: String,
      default: 'base',
    },
    uppercase: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    colorClasses() {
      return {
        gray: 'bg-gray-100 text-gray-800',
        darkGray: 'bg-gray-700 text-gray-200',
        red: 'bg-red-100 text-red-800',
        yellow: 'bg-yellow-100 text-yellow-800',
        green: 'bg-green-100 text-green-800',
        blue: 'bg-blue-100 text-blue-800',
        indigo: 'bg-indigo-100 text-indigo-800',
        primary: 'bg-primary-100 text-primary-800',
        pink: 'bg-pink-100 text-pink-800',
        purple: 'bg-purple-100 text-purple-800',
      }[this.color];
    },

    sizeClasses() {
      return {
        base: 'px-2.5 py-0.5 text-xs',
        lg: 'px-3 py-0.5 text-sm',
        xl: 'px-4 py-1 font-bold text-base',
      }[this.size]
    },

    caseClasses() {
      return this.uppercase ? 'uppercase tracking-wider font-semibold' : 'capitalize'
    }
  },
};
</script>
