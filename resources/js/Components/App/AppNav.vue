<template>
  <div>
    <template v-for="item in navItems">

      <component
        v-show="!item.hidden"
        :is="item.route ? 'inertia-link' : 'div'"
        :class="isCurrentRoute(item.route) ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 text-gray-300 hover:text-white'"
        class="flex items-center group py-2 px-3 my-3 transition rounded-md focus:outline-none cursor-pointer"
        :href="item.route ? $route(item.route) : null"
        @click="item.subItems ? (item.subItemsExpanded = !item.subItemsExpanded) : $emit('close-navigation')">
        <i :class="{'text-primary-500': isCurrentRoute(item.route)}" class="material-icons-outlined mr-3">
          {{ item.icon }}
        </i>
        <div>
          {{ item.title }}
        </div>
        <div class="flex-grow flex items-center justify-end text-right text-gray-300 group-hover:text-white" v-if="item.subItems">
          <i :class="{ 'transform duration-300 rotate-180': item.subItemsExpanded }" class="material-icons-outlined">expand_more</i>
        </div>
      </component>

      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform scale-y-0"
        enter-to-class="transform origin-top scale-y-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform scale-y-100"
        leave-to-class="transform origin-top scale-y-0">

        <div v-if="item.subItemsExpanded">
          <template v-for="subItem in item.subItems">
            <inertia-link
              v-show="!subItem.hidden"
              :class="isCurrentRoute(subItem.route) ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 text-gray-300 hover:text-white'"
              :href="$route(subItem.route)"
              class="flex items-center group py-2 px-3 my-3 transition rounded-md focus:outline-none cursor-pointer"
              @click="$emit('close-navigation')">
              <i :class="{'text-primary-500': isCurrentRoute(subItem.route)}" class="material-icons-outlined mr-3">
                {{ subItem.icon }}
              </i>
              <div>
                {{ subItem.title }}
              </div>
            </inertia-link>
          </template>
        </div>
      </transition>

    </template>

  </div>
</template>

<script>

export default {
  data() {
    return {
      navItems: [
        {
          title: "Dashboard",
          route: "dashboard",
          icon: "dashboard",
        },
        // {
        //   title: "Users",
        //   route: "users.index",
        //   icon: 'supervised_user_circle'
        // },
        // {
        //   title: "Settings",
        //   icon: "settings",
        //   subItemsExpanded: false,
        //   subItems: [
        //
        //   ]
        // }
      ],
      currentRouteName: this.$route().current()
    }
  },

  emits: ['close-navigation'],

  methods: {
    isCurrentRoute(route) {
      if (!route) {
        return false;
      }
      return this.currentRouteName?.split('.')[0] === route.split('.')[0]
    }
  },

  watch: {
    '$page.url': function () {
      this.currentRouteName = this.$route().current()
    }
  }
}
</script>
