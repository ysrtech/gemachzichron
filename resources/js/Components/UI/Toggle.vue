<template>
  <div
    :class="state ? activeColor : inactiveColor"
    :style="toggleStyle"
    class="flex items-center rounded-full transition-colors duration-500"
    @click.self="onClick">
    <div
      :style="draggableStyle"
      class="draggable bg-white rounded-full translate-x-0 transition-transform duration-100 ease-in-out"
      @mousedown.prevent="dragStart">
    </div>
  </div>
</template>

<script>
export default {

  name: 'AppToggle',

  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    height: {
      type: Number,
      default: 20
    },
    activeColor: {
      default: 'bg-primary-600',
    },
    inactiveColor: {
      default: 'bg-gray-300',
    }
  },

  data() {
    return {
      width: 100,
      state: false,
      pressed: 0,
      position: 0
    }
  },

  mounted() {
    this.toggle(this.modelValue)
  },

  watch: {
    position() {
      this.state = this.position >= 50
    }
  },

  computed: {
    toggleStyle() {
      return {
        height: `${this.height * 0.9}px`,
        width: `${this.height * 2}px`
      }
    },
    draggableStyle() {
      return {
        height: `${this.height}px`,
        width: `${this.height}px`,
        transform: `translateX(${this.position / this.width * 100}%)`
      }
    }
  },

  emits: ['update:modelValue'],

  methods: {
    onClick() {
      this.toggle(!this.state)
      this.emit()
    },
    toggle(state) {
      this.state = state
      this.position = !state ? 0 : 100
    },
    dragging(e) {
      const pos = e.clientX - this.$el.offsetLeft
      const percent = pos / this.width * 100
      this.position = percent <= 0 ? 0 : percent >= 100 ? 100 : percent
    },
    dragStart() {
      this.startTimer()
      window.addEventListener('mousemove', this.dragging)
      window.addEventListener('mouseup', this.dragStop)
    },
    dragStop() {
      window.removeEventListener('mousemove', this.dragging)
      window.removeEventListener('mouseup', this.dragStop)
      this.resolvePosition()
      clearInterval(this.$options.interval)
      if (this.pressed < 30) {
        this.toggle(!this.state)
      }
      this.pressed = 0
      this.emit()
    },
    startTimer() {
      this.$options.interval = setInterval(() => this.pressed++, 1)
    },
    resolvePosition() {
      this.position = this.state ? 100 : 0
    },
    emit() {
      this.$emit('update:modelValue', this.state)
    }
  }
}
</script>

<style scoped>
.draggable {
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.5);
}

.draggable:hover {
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.5), 0 0 0 7px rgba(0, 0, 0, 0.05);
}

.draggable:active {
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.5), 0 0 0 7px rgba(0, 0, 0, 0.1);
}
</style>
