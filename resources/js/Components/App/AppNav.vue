<template>
  <nav>
    <template v-for="item in navItems">
      <hr v-if="item.divider" class="border-gray-500">
      <component
        v-else
        v-show="!item.hidden"
        :is="item.route ? 'inertia-link' : 'div'"
        :class="isCurrentRoute(item.route) ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 text-gray-300 hover:text-white'"
        class="flex items-center group py-2 px-3 m-3 transition rounded-md focus:outline-none cursor-pointer"
        :href="item.route ? $route(item.route) : null"
        @click="item.route ? $emit('close-navigation') : null">
        <i :class="{'text-primary-500': isCurrentRoute(item.route)}" class="material-icons-outlined mr-3 text-xl">
          {{ item.icon }}
        </i>
        <div>
          {{ item.title }}
        </div>
      </component>
    </template>
  </nav>
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
          icon: "card_membership",
        },
        {
          title: "Subscriptions",
          route: "subscriptions.index",
          icon: "subscriptions",
        },
        {
          title: "Transactions",
          route: "transactions.index",
          icon: "payments",
        },
        {
          title: "Loans",
          route: "loans.index",
          icon: "account_balance",
        },
        { divider: true },
        {
          title: "Data Export",
          route: "export.index",
          icon: 'download',
        },
        {
          title: "Users",
          route: "users.index",
          icon: 'supervised_user_circle',
        },

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
