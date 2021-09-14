<template>
  <nav class="flex flex-col justify-between min-h-full">
    <span v-for="items in {topItems, bottomItems}">
      <template v-for="item in items">
        <hr class="border-gray-500" v-if="item.divide">
        <inertia-link
          v-show="!item.hidden"
          :class="item.active() ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 text-gray-300 hover:text-white'"
          class="flex items-center group py-2 px-3 m-3 transition rounded-md focus:outline-none cursor-pointer"
          :href="$route(item.route)"
          @click="$emit('item-clicked', item)">
          <i :class="{'text-primary-500': item.active()}" class="material-icons-outlined mr-3 text-xl">
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
          active: () => this.currentRouteName === 'dashboard'
        },
        {
          title: "Members",
          route: "members.index",
          icon: "people",
          active: () => this.currentRouteName.startsWith('members.'),
        },
        {
          title: "Memberships",
          route: "memberships.index",
          icon: "card_membership",
          active: () => this.currentRouteName.startsWith('memberships.'),
        },
        {
          title: "Subscriptions",
          route: "subscriptions.index",
          icon: "subscriptions",
          active: () => this.currentRouteName.startsWith('subscriptions.'),
        },
        {
          title: "Transactions",
          route: "transactions.index",
          icon: "payments",
          active: () => this.currentRouteName.startsWith('transactions.'),
        },
        {
          title: "Loans",
          route: "loans.index",
          icon: "account_balance",
          active: () => this.currentRouteName.startsWith('loans.'),
        },
        {
          divide: true,
          title: "Data Export",
          route: "export.index",
          icon: 'download',
          active: () => this.currentRouteName === 'export.index',
        },
        {
          title: "Users",
          route: "users.index",
          icon: 'supervised_user_circle',
          active: () => this.currentRouteName.startsWith('users.'),
        },

      ],
      bottomItems: [
        {
          divide: true,
          title: "Settings",
          route: "login",
          icon: 'settings',
          active: () => false
        },
      ],
    }
  },

  emits: ['item-clicked'],

  computed: {
    currentRouteName() {
      this.$page.url // trigger update
      return this.$route().current()
    }
  },
}
</script>
