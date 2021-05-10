<template>
  <div>
    <div class="border-b border-gray-300 mb-6">
      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <inertia-link
          v-for="tab in tabs"
          :key="tab.title"
          :href="$route(tab.route, member.id)"
          class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm"
          :class="[$route().current(tab.route) ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300']">
          {{ tab.title }}
        </inertia-link>
      </nav>
    </div>
    <slot/>
    <teleport v-if="isMounted" to="#header">
      {{ member.title + ' ' + member.first_name + ' ' + member.last_name }}
      <app-badge color="red" v-if="member.deleted_at">Archived Member</app-badge>
    </teleport>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import AppBadge from "@/Components/UI/Badge";
import IsMounted from "@/Mixins/IsMounted";

export default {
  components: {AppBadge},

  layout: AppLayout,

  mixins: [IsMounted],

  props: {
    member: Object,
  },

  data() {
    return {
      tabs: [
        {
          title: 'Details',
          route: 'members.show',
        },
        {
          title: 'Children',
          route: 'members.dependents.index',
        },
        {
          title: 'Membership',
          route: 'members.membership.show',
        },
        // {
        //   title: 'Subscriptions',
        //   route: '',
        // },
        // {
        //   title: 'Loans',
        //   route: '',
        // },
      ]
    }
  },
}
</script>
