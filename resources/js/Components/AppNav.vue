<template>
  <nav class="flex flex-col justify-between min-h-full">
    <span v-for="items in {topItems, bottomItems}">
      <template v-for="item in items">
        <hr class="border-gray-500" v-if="item.divide">
        <inertia-link
          v-show="!item.hidden"
          :class="isCurrentRoute(item.route) ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 text-gray-300 hover:text-white'"
          class="flex items-center group py-2 px-3 m-3 transition rounded-md focus:outline-none cursor-pointer"
          :href="$route(item.route)"
          @click="$emit('item-clicked', item)">
          <i :class="{'text-primary-500': isCurrentRoute(item.route)}" class="material-icons-outlined mr-3 text-xl">
            {{ item.icon }}
          </i>
          <div>{{ item.title }}</div>
        </inertia-link>
      </template>
    </span>
  </nav>
</template>

<script>

export default {
  data() {
    return {
      topItems: [
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
        {
          divide: true,
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
      bottomItems: [
        {
          divide: true,
          title: "Settings",
          route: "login",
          icon: 'settings',
        },
      ],
      currentRouteName: this.$route().current()
    }
  },

  emits: ['item-clicked'],

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
