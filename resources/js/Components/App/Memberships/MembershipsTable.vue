<template>
  <div>
    <table class="min-w-full divide-y divide-gray-200">

      <thead>
      <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
        <th
          v-for="title in ['Member', 'Membership Type', 'Plan Type', 'Membership Since', 'Total Paid']"
          class="px-6 py-3 font-medium">{{ title }}
        </th>
      </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
      <tr
        @click="$inertia.get($route('members.show', member.id))"
        v-for="member in members.data"
        :key="member.id"
        class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
        <td class="px-6 py-3.5 whitespace-nowrap flex space-x-1 items-baseline">
          <span class="font-medium">{{ member.last_name + ', ' + member.first_name }}</span>
          <app-badge v-if="member.deleted_at" color="red">Archived Member</app-badge>
          <app-badge v-if="member.membership?.is_active === false" color="red">Inactive Membership</app-badge>
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.membership_type }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ member.plan_type?.name }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ date(member.membership_since) }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <money
            :amount="member.membership_payments_total || 0"
            class="font-medium"
            currency-sign-class="font-normal text-gray-600 mr-1"/>
        </td>
      </tr>

      <tr v-if="members.total === 0">
        <td class="border-t px-6 py-10 text-center text-gray-500" colspan="6">No memberships found.</td>
      </tr>

      </tbody>
    </table>

    <!-- Pagination -->
    <div
      v-if="members.total > members.per_page"
      class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-300 sm:px-6">
      <app-pagination :response="members"/>
    </div>
  </div>
</template>

<script>
import AppDropdown from "@/Components/Dropdown";
import AppDropdownLink from "@/Components/DropdownLink";
import AppPagination from "@/Components/Pagination";
import {date} from "@/helpers/dates";
import Money from "@/Components/Money";

export default {
  components: {
    Money,
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
