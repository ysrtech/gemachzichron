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
          active: () => this.$page.component === 'Dashboard'
        },
        {
          title: "Members",
          route: "members.index",
          icon: "people",
          active: () => this.$page.component.startsWith('Members/'),
        },
        {
          title: "Memberships",
          route: "memberships.index",
          icon: "card_membership",
          active: () => this.$page.component.startsWith('Memberships/'),
        },
        {
          title: "Subscriptions",
          route: "subscriptions.index",
          icon: "subscriptions",
          active: () => this.$page.component.startsWith('Subscriptions/'),
        },
        {
          title: "Transactions",
          route: "transactions.index",
          icon: "payments",
          active: () => this.$page.component.startsWith('Transactions/'),
        },
        {
          title: "Loans",
          route: "loans.index",
          icon: "account_balance",
          active: () => this.$page.component.startsWith('Loans/'),
        },
        {
          divide: true,
          title: "Conflicts",
          route: "conflicts.index",
          icon: 'error_outline',
          active: () => this.$page.component.startsWith('GatewayConflicts/'),
        },
        {
          title: "Activity Logs",
          route: "activity-logs.index",
          icon: 'history',
          active: () => this.$page.component.startsWith('ActivityLogs/'),
        },
        {
          title: "Mail Logs",
          route: "mail-logs.index",
          icon: 'email',
          active: () => this.$page.component.startsWith('MailLogs/'),
        },
        {
          title: "Data Export",
          route: "export.index",
          icon: 'download',
          active: () => this.$page.component === 'DataExport/Index',
        },
        {
          title: "Users",
          route: "users.index",
          icon: 'supervised_user_circle',
          active: () => this.$page.component.startsWith('Users/'),
        },

      ],
      bottomItems: [
        {
          divide: true,
          title: "Settings",
          route: "settings.index",
          icon: 'settings',
          active: () => this.$page.component === 'Settings/Index'
        },
      ],
    }
  },

  emits: ['item-clicked'],

}
</script>
