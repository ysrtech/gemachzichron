<template>
  <div>
    <table class="min-w-full divide-y divide-gray-200">

      <thead>
      <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
        <th v-for="title in ['Member', 'Membership Type', 'Plan Type', 'Membership Since', 'Total Paid']" class="px-6 py-3 font-medium">{{ title }}</th>
      </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
      <tr
        @click="$inertia.get($route('members.show', member.id))"
        v-for="member in members.data"
        :key="member.id"
        class="bg-white text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.last_name + ', ' + member.first_name }}
          <app-badge v-if="member.deleted_at" color="red" class="ml-1">Archived Member</app-badge>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.membership.type }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.membership.plan_type?.name }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ date(member.membership.created_at) }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <span v-if="member.membership.total_paid">$</span>
          {{ member.membership.total_paid }}
        </td>
      </tr>

      <tr v-if="members.total === 0">
        <td class="border-t px-6 py-10 text-center text-gray-500" colspan="6">No memberships found.</td>
      </tr>

      </tbody>
    </table>

    <!-- Pagination -->
    <div v-if="members.total > members.per_page" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-300 sm:px-6">
      <app-pagination :response="members"/>
    </div>
  </div>
</template>

<script>
import AppDropdown from "@/Components/UI/Dropdown";
import AppDropdownLink from "@/Components/UI/DropdownLink";
import AppPagination from "@/Components/App/Pagination";
import AppBadge from "@/Components/UI/Badge";
import {date} from "@/helpers/dates";

export default {
  components: {
    AppBadge,
    AppPagination,
    AppDropdownLink,
    AppDropdown
  },

  props: {
    members: Object
  },

  methods: {
    date
  }
}
</script>
