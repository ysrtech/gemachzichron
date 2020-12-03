<template>
  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full whitespace-no-wrap">
      <tr class="text-left text-xs text-gray-400 uppercase">
        <th class="px-6 py-4 font-medium">Member</th>
<!--        <th class="px-6 py-4 font-medium">Subscription</th>-->
        <th class="px-6 py-4 font-medium">Date</th>
        <th class="px-6 py-4 font-medium">Amount</th>
        <th class="px-6 py-4 font-medium">Total</th>
        <th class="px-6 py-4 font-medium">Date Paid</th>
        <th class="px-6 py-4 font-medium" colspan="2">Status</th>
      </tr>
      <tr v-for="invoice in invoices" :key="invoice.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-3">
          <inertia-link :href="$route('members.show', {member: invoice.subscription.membership.member_id})" class="hover:underline">
            {{ invoice.subscription.membership.member.first_name }}
            {{ invoice.subscription.membership.member.last_name }}
          </inertia-link>
        </td>
<!--        <td class="border-t px-6 py-3">{{ invoice.subscription_id }}</td>-->
        <td class="border-t px-6 py-3">{{ formatDate(invoice.created_at) }}</td>
        <td class="border-t px-6 py-3">${{ invoice.amount }}</td>
        <td class="border-t px-6 py-3">${{ invoice.total }}</td>
        <td class="border-t px-6 py-3">{{ formatDate(invoice.payment_date) }}</td>
        <td class="border-t px-6 py-3 flex">
          <div
            :class="[invoice.status ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200']"
            class="px-3 rounded-full text-sm">
            {{ invoice.status ? 'Paid' : 'Pending' }}
          </div>
        </td>
        <td class="border-t px-6 w-px py-0">
          <div class="px-2 flex items-center">
            <inertia-link :href="$route('invoices.show', {invoice: invoice.id})" class="material-icons focus:outline-none rounded-full p-2 text-gray-500 hover:bg-gray-200 focus:bg-gray-300">
              launch
            </inertia-link>
          </div>
        </td>
      </tr>
      <tr v-if="invoices.length === 0">
        <td class="border-t px-6 py-10 text-center" colspan="7">No invoices found.</td>
      </tr>
    </table>

  </div>

</template>

<script>
import AppDropdown from "../../../Shared/Dropdown";
import AppDropdownLink from "../../../Shared/DropdownLink";

export default {
  name: "InvoicesTable",

  components: {
    AppDropdown,
    AppDropdownLink,
  },

  props: {
    invoices: Array
  },

}
</script>
