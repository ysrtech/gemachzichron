<template>
  <div class="max-w-3xl mx-auto">
    <search-filter
      v-model="filterForm.search"
      :applied-filters-length="appliedFiltersLength"
      class="w-full max-w-md mb-6"
      placeholder="Search By Member..."
      @reset="reset">

      <search-filter-field
        v-model="filterForm.amount"
        type="number"
        label="Amount"
      />

      <search-filter-field
        v-model="filterForm.from_date"
        type="date"
        label="From Date"
      />

      <search-filter-field
        v-model="filterForm.to_date"
        type="date"
        label="To Date"
      />
    </search-filter>

    <app-panel>
      <template #content>
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
                class="font-medium hover:text-gray-900 hover:underline"
                :href="$route('members.show', loan.member.id)">
                {{ loan.member.first_name + ' ' + loan.member.last_name }}
              </inertia-link>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap font-medium">
              <money :amount="loan.amount"/>
            </td>
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
      </template>
    </app-panel>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import LinksPagination from "@/Components/App/LinksPagination";
import Money from "@/Components/UI/Money";
import AppPanel from "@/Components/UI/Panel";
import SearchFilter from "@/Components/App/SearchFilter";
import SearchFilterField from "@/Components/App/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Loans'}, () => page),

  components: {
    SearchFilterField,
    SearchFilter,
    AppPanel,
    Money,
    LinksPagination
  },

  data() {
    return {
      filterForm: {
        search: this.filters.search,
        amount: this.filters.amount,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
      }
    }
  },

  mixins: [HasFilters],

  props: {
    loans: Object,
    filters: Object,
  },

  methods: {
    date,
  }
}
</script>

