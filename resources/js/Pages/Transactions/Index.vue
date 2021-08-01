<template>
  <div class="max-w-7xl mx-auto">
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
        v-model="filterForm.subscription_id"
        type="number"
        label="Subscription ID"
      />

      <search-filter-field
        v-model="filterForm.status"
        type="select"
        label="Status"
        :options="Object.assign({'All': ''}, TRANSACTION_STATUSES)"
      />

      <search-filter-field
        v-model="filterForm.type"
        type="select"
        label="Type"
        :options="Object.assign({'All': ''}, TRANSACTION_TYPES)"
      />

      <search-filter-field
        v-model="filterForm.gateway"
        type="select"
        label="Gateway"
        :options="Object.assign({'All': ''}, AVAILABLE_GATEWAYS)"
      />

      <search-filter-field
        v-model="filterForm.gateway_identifier"
        type="text"
        label="Gateway ID"
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
        <transactions-table :transactions="transactions.data" show-member/>

        <div v-if="transactions.total === 0" class="px-6 py-10 text-center text-gray-500">No Transactions Found.</div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
          <links-pagination :links="transactions.links"/>
        </div>
      </template>
    </app-panel>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import LinksPagination from "@/Components/LinksPagination";
import AppPanel from "@/Components/Panel";
import SearchFilter from "@/Components/SearchFilter";
import SearchFilterField from "@/Components/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";
import {TRANSACTION_STATUSES, TRANSACTION_TYPES} from "@/config/transactions";
import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import TransactionsTable from "@/Components/App/Transactions/TransactionsTable";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Transactions'}, () => page),

  components: {
    TransactionsTable,
    SearchFilterField,
    SearchFilter,
    AppPanel,
    LinksPagination
  },

  data() {
    return {
      filterForm: {
        search: this.filters.search,
        amount: this.filters.amount,
        subscription_id: this.filters.subscription_id,
        status: this.filters.status,
        type: this.filters.type,
        gateway: this.filters.gateway,
        gateway_identifier: this.filters.gateway_identifier,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
      },
      TRANSACTION_STATUSES,
      TRANSACTION_TYPES,
      AVAILABLE_GATEWAYS,
    }
  },

  mixins: [HasFilters],

  props: {
    transactions: Object,
    filters: Object,
  },
}
</script>

