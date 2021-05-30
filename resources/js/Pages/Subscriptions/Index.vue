<template>
  <div class="max-w-7xl mx-auto">
    <search-filter v-model="filterForm.search" class="w-full max-w-md mb-6" placeholder="Search By Member..." @reset="reset">
      <div class="p-4 space-y-4">

        <search-filter-field
          v-model="filterForm.amount"
          type="number"
          label="Amount"
        />

        <search-filter-field
          v-model="filterForm.type"
          type="select"
          label="Type"
          :options="Object.assign({'All': null}, SUBSCRIPTION_TYPES)"
        />

        <search-filter-field
          v-model="filterForm.active"
          type="select"
          label="Status"
          :options="{'All': null, 'Active': '1', 'Inactive': '0'}"
        />

        <search-filter-field
          v-model="filterForm.gateway"
          type="select"
          label="Gateway"
          :options="Object.assign({'All': null}, AVAILABLE_GATEWAYS)"
        />

      </div>
    </search-filter>

    <app-panel class="overflow-x-auto">
      <template #content>
        <subscriptions-table :subscriptions="subscriptions.data" show-member/>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
          <links-pagination :links="subscriptions.links"/>
        </div>
      </template>
    </app-panel>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import LinksPagination from "@/Components/App/LinksPagination";
import AppPanel from "@/Components/UI/Panel";
import SearchFilter from "@/Components/App/SearchFilter";
import SearchFilterField from "@/Components/App/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";
import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import {SUBSCRIPTION_TYPES} from "@/config/subscriptions";
import SubscriptionsTable from "@/Pages/Subscriptions/SubscriptionsTable";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Subscriptions'}, () => page),

  components: {
    SubscriptionsTable,
    SearchFilterField,
    SearchFilter,
    AppPanel,
    LinksPagination,
  },

  data() {
    return {
      filterForm: {
        search: this.filters.search,
        amount: this.filters.amount,
        type: this.filters.type,
        active: this.filters.active,
        gateway: this.filters.gateway,
      },
      AVAILABLE_GATEWAYS,
      SUBSCRIPTION_TYPES,
    }
  },

  mixins: [HasFilters],

  props: {
    subscriptions: Object,
    filters: Object,
  },
}
</script>

