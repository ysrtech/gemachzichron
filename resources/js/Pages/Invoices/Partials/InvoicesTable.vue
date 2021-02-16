<template>
  <div>
    <table class="min-w-full divide-y divide-gray-200">

      <thead>
      <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
        <th v-for="title in ['Member', 'Date', 'Amount', 'Total', 'Payment Date', 'Status', '']" class="px-6 py-3 font-medium">{{ title }}</th>
      </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
      <tr
        v-for="invoice in invoices.data"
        :key="invoice.id"
        class="bg-white text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ invoice.subscription.membership.member.first_name + ' ' + invoice.subscription.membership.member.last_name }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ date(invoice.created_at) }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          ${{ invoice.amount }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          ${{ invoice.total }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          {{ date(invoice.payment_date) }}
        </td>
        <td class="px-6 py-3.5 whitespace-nowrap">
          <app-badge :color="invoice.status ? 'green' : 'red'">{{ invoice.status ? 'Paid' : 'Pending' }}</app-badge>
        </td>
        <td class="px-6 text-right w-px whitespace-nowrap text-sm text-gray-500">
          <inertia-link :href="$route('invoices.show', invoice.id)">
            <button class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
              launch
            </button>
          </inertia-link>
        </td>
      </tr>

      <tr v-if="invoices.total === 0">
        <td class="border-t px-6 py-10 text-center text-gray-500" colspan="7">No invoices found.</td>
      </tr>

      </tbody>
    </table>

    <!-- Pagination -->
    <div v-if="invoices.total > invoices.per_page" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-300 sm:px-6">
      <app-pagination :response="invoices"/>
    </div>
  </div>
</template>

<script>
import AppPagination from "@/Components/App/Pagination";
import AppBadge from "@/Components/UI/Badge";
import {date} from "@/helpers/dates";

export default {
  components: {
    AppBadge,
    AppPagination,
  },

  props: {
    invoices: Object
  },

  methods: {
    date
  }
}
</script>
