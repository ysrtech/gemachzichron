<template>
  <nav class="flex flex-col justify-between min-h-full">
    <span v-for="items in {topItems, bottomItems}" :key="items === topItems ? 'top' : 'bottom'">
      <template v-for="item in items" :key="item.title">
        <hr class="border-gray-500" v-if="item.divide">

        <!-- Dropdown group -->
        <details v-if="item.children" :open="isGroupOpen(item)" class="m-3">
          <summary
            class="flex items-center justify-between py-2 px-3 rounded-md cursor-pointer hover:bg-gray-800 text-gray-300 hover:text-white"
            :class="isGroupOpen(item) ? 'bg-gray-700 text-white' : ''">
            <div class="flex items-center">
              <i class="material-icons-outlined mr-3 text-xl">{{ item.icon }}</i>
              <div>{{ item.title }}</div>
            </div>
            <i
              class="material-icons-outlined text-base transition-transform duration-150"
              :class="isGroupOpen(item) ? 'rotate-90' : ''"
            >chevron_right</i>
          </summary>
          <div class="mt-2 ml-4 space-y-1">
            <inertia-link
              v-for="child in item.children"
              :key="child.title"
              v-show="!child.hidden"
              :href="$route(child.route)"
              :class="child.active() ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 text-gray-300 hover:text-white'"
              class="flex items-center group py-2 px-3 transition rounded-md focus:outline-none cursor-pointer"
              @click="$emit('item-clicked', child)">
              <i :class="{'text-primary-500': child.active()}" class="material-icons-outlined mr-3 text-xl">{{ child.icon }}</i>
              <div>{{ child.title }}</div>
            </inertia-link>
          </div>
        </details>

        <!-- Single link -->
        <inertia-link
          v-else
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
          title: "Clients",
          route: "members.index",
          icon: "people",
          active: () => this.$page.component.startsWith('Members/'),
        },
        {
          title: "Loans",
          route: "loans.index",
          icon: "account_balance",
          active: () => this.$page.component.startsWith('Loans/'),
        },
        {
          title: "Permissions",
          icon: 'admin_panel_settings',
          active: () => false,
          children: [
            {
              title: "Users",
              route: "users.index",
              icon: 'supervised_user_circle',
              active: () => this.$page.component.startsWith('Users/'),
            },
            {
              title: "Roles",
              route: "roles.index",
              icon: 'shield',
              active: () => this.$page.component.startsWith('Roles/'),
            },
          ]
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

  methods: {
    isGroupOpen(item) {
      if (!item.children) return false;
      return item.children.some(child => child.active());
    },
  }

}
</script>
