<template>
  <inertia-link
    class="flex items-center group py-3 transition rounded-md group" :href="route(routeName)">
    <icon :name="icon" class="w-4 h-4 mr-2"
          :class="$page.currentRouteName == routeName ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'"/>
    <div :class="$page.currentRouteName == routeName ? 'text-white' : 'text-indigo-300 group-hover:text-white'">{{ title }}</div>
  </inertia-link>
</template>

<script>
import Icon from "./Icon";

export default {
  name: "SideNavItem",
  components: {
    Icon,
  },
  props: {
    title: String,
    routeName: String,
    icon: String
  },
}
</script>
