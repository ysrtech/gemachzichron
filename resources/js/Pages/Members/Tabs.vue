<template>
  <div>
    <div class="border-b border-gray-300">
      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <inertia-link
          v-for="tab in tabs"
          :key="tab.title"
          :href="$route(tab.route, member.id)"
          class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
          :class="[$route().current(tab.route) ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300']">
          {{ tab.title }}
        </inertia-link>
      </nav>
    </div>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {

  layout: AppLayout,

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
        // {
        //   title: 'Dependents',
        //   route: '',
        // },
        // {
        //   title: 'Membership',
        //   route: '',
        // },
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
