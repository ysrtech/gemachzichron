<template>
  <th
    class="px-6 py-3"
    :class="[
      active ? 'font-bold text-gray-600' : 'font-medium',
      name ? 'cursor-pointer hover:bg-gray-100' : ''
    ]"
    @click="update"
  >
    <span class="flex items-center space-x-1">
      <span>{{ value }}</span>
      <span v-if="active" class="material-icons-outlined text-sm -my-1">
        {{ current === name ? 'expand_less' : 'expand_more' }}
      </span>
    </span>
  </th>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  value: {
    type: String,
    default: null
  },
  name: {
    type: String,
    default: null
  },
  current: {
    type: String,
    default: null,
  },
})

const active = computed(() => {
  if (!props.name) {
    return false
  }
  if (props.current === props.name) {
    return true
  }

  let items = props.name.split(',')

  return items.map(item => `-${item}`).join(',') === props.current
})

const emit = defineEmits(['sort'])

function update() {
  if (!props.name) return

  if (props.current === props.name) {
    emit('sort', props.name.split(',').map(item => `-${item}`).join(','))
  } else if (active.value) { // current is desc
    emit('sort', null)
  } else {
    emit('sort', props.name)
  }
}
</script>
