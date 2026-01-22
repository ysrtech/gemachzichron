<template>
  <nav class="flex flex-col justify-between min-h-full px-3">
    <span v-for="items in {topItems, bottomItems}" :key="items === topItems ? 'top' : 'bottom'">
      <template v-for="item in items" :key="item.title">
        <hr class="border-zinc-700 my-4" v-if="item.divide">

        <!-- Dropdown group -->
        <details v-if="item.children" :open="isGroupOpen(item)" class="mb-1">
          <summary
            class="flex items-center justify-between py-2.5 px-3 rounded-lg cursor-pointer hover:bg-white/5 text-zinc-400 hover:text-white transition-colors"
            :class="isGroupOpen(item) ? 'bg-white/10 text-white' : ''">
            <div class="flex items-center">
              <i class="material-icons-outlined mr-3 text-[20px]">{{ item.icon }}</i>
              <div class="text-sm font-medium">{{ item.title }}</div>
            </div>
            <i
              class="material-icons-outlined text-base transition-transform duration-200"
              :class="isGroupOpen(item) ? 'rotate-90' : ''"
            >chevron_right</i>
          </summary>
          <div class="mt-1 ml-2 space-y-0.5">
            <inertia-link
              v-for="child in item.children"
              :key="child.title"
              v-show="!child.hidden"
              :href="$route(child.route)"
              :class="child.active() ? 'bg-white/10 text-white' : 'hover:bg-white/5 text-zinc-400 hover:text-white'"
              class="flex items-center group py-2 px-3 transition-colors rounded-lg focus:outline-none cursor-pointer"
              @click="$emit('item-clicked', child)">
              <i :class="{'text-primary-400': child.active()}" class="material-icons-outlined mr-3 text-[20px]">{{ child.icon }}</i>
              <div class="text-sm font-medium">{{ child.title }}</div>
            </inertia-link>
          </div>
        </details>

        <!-- Single link -->
        <div
          v-else-if="item.disabled"
          class="flex items-center group py-2.5 px-3 mb-1 transition-colors rounded-lg cursor-pointer hover:bg-white/5 text-zinc-400 hover:text-white"
        >
          <i class="material-icons-outlined mr-3 text-[20px]">
            {{ item.icon }}
          </i>
          <div class="text-sm font-medium">{{ item.title }}</div>
        </div>
        <inertia-link
          v-else
          :class="item.active() ? 'bg-white/10 text-white' : 'hover:bg-white/5 text-zinc-400 hover:text-white'"
          class="flex items-center group py-2.5 px-3 mb-1 transition-colors rounded-lg focus:outline-none cursor-pointer"
          :href="$route(item.route)"
          @click="$emit('item-clicked', item)">
          <i :class="{'text-primary-400': item.active()}" class="material-icons-outlined mr-3 text-[20px]">
            {{ item.icon }}
          </i>
          <div class="text-sm font-medium">{{ item.title }}</div>
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
          disabled: true,
        },
        {
          title: "Loans",
          route: "loans.index",
          icon: "account_balance",
          active: () => this.$page.component.startsWith('Loans/'),
          disabled: true,
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
          disabled: true,
        },
      ],
      bottomItems: [
        {
          divide: true,
          title: "Settings",
          route: "settings.index",
          icon: 'settings',
          active: () => this.$page.component === 'Settings/Index',
          disabled: true,
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
