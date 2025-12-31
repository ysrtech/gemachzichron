<template>
  <div class="max-w-12xl mx-auto">
    <search-filter
      v-model="filterForm.search"
      :applied-filters-length="appliedFiltersLength"
      class="w-full max-w-md mb-6"
      placeholder="Search By Member..."
      @reset="reset">

      <search-filter-field
        v-model="filterForm.loan_type"
        type="select"
        label="Loan Type"
        :options="loanTypeOptions"
      />

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
            <table-header
              v-for="header in headers"
              :key="header.name"
              :name="header.name"
              :value="header.value"
              :current="filterForm.sort"
              v-show="!header.hidden"
              @sort="filterForm.sort = $event"
            />
          </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
          <inertia-link
            as="tr"
            :href="$route('loans.show', loan.id)"
            v-for="loan in loans.data"
            :key="loan.id"
            class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
            <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ loan.id }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap space-x-2 font-medium">
              <inertia-link
                @click.stop
                class="font-medium hover:text-gray-900 hover:underline"
                :href="$route('members.show', loan.member.id)">
                {{ loan.member.first_name + ' ' + loan.member.last_name }}
              </inertia-link>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ loan.loan_type || '-' }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ loan.dependent?.name || '-' }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap font-medium text-right">
              <money :amount="loan.amount"/>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap text-right">
              <money :amount="loan.amount - loan.transactions_sum_amount"/>
            </td>
          </inertia-link>

          <tr v-if="loans.total === 0">
            <td class="px-6 py-10 text-center text-gray-500" colspan="7">No Loans Found.</td>
          </tr>

          </tbody>
        </table>

        <!-- Pagination -->
        <div
          v-if="loans.total > loans.per_page"
          class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
          <pagination :response="loans"/>
        </div>
      </template>
    </app-panel>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import Pagination from "@/Components/Pagination";
import Money from "@/Components/Money";
import AppPanel from "@/Components/Panel";
import SearchFilter from "@/Components/SearchFilter";
import SearchFilterField from "@/Components/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";
import TableHeader from "@/Components/TableHeader.vue";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Loans'}, () => page),

  components: {
    TableHeader,
    SearchFilterField,
    SearchFilter,
    AppPanel,
    Money,
    Pagination,
  },

  data() {
    return {
      headers: [
        {value: 'ID', name: 'id'},
        {value: 'Member', name: 'member_last_name,member_first_name'},
        {value: 'Loan Type', name: 'loan_type'},
        {value: 'Dependent', name: 'dependent_name'},
        {value: 'Loan Date', name: 'loan_date'},
        {value: 'Amount', name: 'amount'},
        {value: 'Remaining Balance', name: 'transactions_sum_amount'},
      ],
      filterForm: {
        sort: this.filters.sort,
        search: this.filters.search,
        loan_type: this.filters.loan_type,
        amount: this.filters.amount,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
      },
      loanTypes: []
    }
  },

  mixins: [HasFilters],

  props: {
    loans: Object,
    filters: Object,
  },

  mounted() {
    this.fetchLoanTypes();
  },

  methods: {
    date,
    async fetchLoanTypes() {
      try {
        const res = await this.$axios.get(this.$route('ajax.loan-types.index'));
        this.loanTypes = res.data;
      } catch (error) {
        console.error('Failed to fetch loan types:', error);
      }
    }
  },

  computed: {
    loanTypeOptions() {
      const options = { 'All': '' };
      this.loanTypes.forEach(type => {
        options[type.name] = type.name;
      });
      return options;
    }
  }
}
</script>

