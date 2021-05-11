<template>
  <div>
      <div class="max-w-3xl mx-auto bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead v-if="loans.total > 0">
          <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
            <th v-for="title in ['ID', 'Member', 'Loan date', 'Amount', '']" class="px-6 py-3 font-medium">{{ title }}</th>
          </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="loan in loans.data"
            :key="loan.id"
            class="bg-white text-sm text-gray-700">
            <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ loan.id }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap space-x-2 font-medium">
              <inertia-link
                @click.stop
                class="font-medium hover:text-gray-900"
                :href="$route('members.show', loan.membership.member.id)">
                {{ loan.membership.member.first_name + ' ' + loan.membership.member.last_name }}
              </inertia-link>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap font-medium"><span class="mr-1 text-gray-500">$</span>{{ Number(loan.amount).toFixed(2) }}</td>
            <td class="px-6 whitespace-nowrap text-right">
              <inertia-link
                class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
                :href="$route('loans.show', loan.id)">
                launch
              </inertia-link>
            </td>
          </tr>

          <tr v-if="loans.total === 0">
            <td class="px-6 py-10 text-center text-gray-500" colspan="3">No Loans Found.</td>
          </tr>

          </tbody>
        </table>

        <!-- Pagination -->
        <div
          v-if="loans.total > loans.per_page"
          class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
          <links-pagination :links="loans.links"/>
        </div>
      </div>

  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import LinksPagination from "@/Components/App/LinksPagination";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Loans'}, () => page),

  components: {
    LinksPagination
  },

  props: {
    loans: Object,
  },

  methods: {
    date,
  }
}
</script>

