<template>
<div>
  <template v-for="item in navItems">
    <inertia-link
      preserve-state
      v-if="!item.subItems"
      class="flex items-center group py-2 px-3 my-2 transition rounded-md group focus:outline-none cursor-pointer"
      :class="isCurrentRoute(item.route) ? 'bg-primary-700' : 'hover:bg-primary-900'"
      :href="$route(item.route)">
      <i class="material-icons-outlined text-primary-400 mr-2">
        {{ item.icon }}
      </i>
      <div
        :class="isCurrentRoute(item.route) ? 'text-white' : 'text-primary-300 group-hover:text-white'">
        {{ item.title }}
      </div>
    </inertia-link>

    <inertia-link
      v-else
      class="flex items-center group py-2 px-3 my-2 transition rounded-md group focus:outline-none cursor-pointer"
      :class="isCurrentRoute(item.route) ? 'bg-primary-700' : 'hover:bg-primary-900'"
      :as="!item.route ? 'div' : 'a'"
      @click.prevent="item.subItemsExpanded = !item.subItemsExpanded" href="#">
      <i class="material-icons-outlined mr-2 text-primary-400 group-hover:text-white">
        {{ item.icon }}
      </i>
      <div class="text-primary-300 group-hover:text-white">
        {{ item.title }}
      </div>
      <div class="flex-grow flex items-center justify-end text-right text-primary-300 group-hover:text-white">
        <i class="material-icons" :class="{ 'transform duration-300 rotate-180': item.subItemsExpanded }">expand_more</i>
      </div>
    </inertia-link>

    <transition
      enter-class="transform scale-y-0"
      enter-active-class="transition ease-out duration-300"
      enter-to-class="transform origin-top scale-y-100"
      leave-class="transform scale-y-100"
      leave-active-class="transition ease-in duration-200"
      leave-to-class="transform origin-top scale-y-0">

      <div v-if="item.subItemsExpanded">
        <template v-for="subItem in item.subItems">
          <inertia-link
            preserve-state
            class="flex items-center group py-2 px-3 my-2 transition rounded-md group focus:outline-none"
            :class="isCurrentRoute(subItem.route) ? 'bg-primary-700' : 'hover:bg-primary-900'"
            :href="$route(subItem.route)">
            <i
              class="material-icons-outlined text-primary-400 mr-2"
              v-show="subItem.icon">
              {{ subItem.icon }}
            </i>
            <div
              :class="isCurrentRoute(subItem.route) ? 'text-white' : 'text-primary-300 group-hover:text-white'">
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
        {
          title: "Members",
          route: "members.index",
          icon: "people",
        },
        {
          title: "Memberships",
          route: "memberships.index",
          icon: "payments",
        },
        {
          title: "Invoices",
          route: "invoices.index",
          icon: "receipt_long",
        },
        {
          title: "Users",
          route: "users.index",
          icon: 'supervised_user_circle'
        },
        {
          title: "Settings",
          icon: "settings",
          subItemsExpanded: false,
          subItems: [

          ]
        }
      ]
    }
  },

  methods: {
    isCurrentRoute(route) {
      if (!route) {
        return false;
      }
      return this.$page.props.currentRouteName?.split('.')[0] === route.split('.')[0]
    }
  }
}
</script>
