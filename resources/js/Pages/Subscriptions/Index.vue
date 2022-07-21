<template>
  <div class="max-w-12xl mx-auto">

    <alert
      v-if="missing_subscriptions_count"
      :header="`${missing_subscriptions_count} missing subscriptions from gateways`"
      link-text="View"
      :link="$route('conflicts.index', {tab: 'Missing Subscriptions'})"
      class="mb-6"
    />

    <div class="mb-6 flex justify-between items-center">
        <search-filter
          v-model="filterForm.search"
          :applied-filters-length="appliedFiltersLength"
          class="w-full max-w-md"
          placeholder="Search By Member..."
          @reset="reset">

          <search-filter-field
            v-model="filterForm.amount"
            type="number"
            label="Amount"
          />

          <search-filter-field
            v-model="filterForm.type"
            type="select"
            label="Type"
            :options="Object.assign({'All': ''}, SUBSCRIPTION_TYPES)"
          />

          <search-filter-field
            v-model="filterForm.active"
            type="select"
            label="Status"
            :options="{'All': '', 'Active': '1', 'Inactive': '0'}"
          />

          <search-filter-field
            v-model="filterForm.gateway"
            type="select"
            label="Gateway"
            :options="Object.assign({'All': ''}, AVAILABLE_GATEWAYS)"
          />
        </search-filter>

      <app-button @click="subscriptionFormModalOpen = true">New Subscription</app-button>
    </div>

    <app-panel class="overflow-x-auto">
      <template #content>
        <subscriptions-table :subscriptions="subscriptions.data" show-member/>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
          <pagination :response="subscriptions"/>
        </div>
      </template>
    </app-panel>

    <subscription-form-modal
      :show="subscriptionFormModalOpen"
      @close="subscriptionFormModalOpen = false"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import AppPanel from "@/Components/Panel";
import SearchFilter from "@/Components/SearchFilter";
import SearchFilterField from "@/Components/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";
import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import {SUBSCRIPTION_TYPES} from "@/config/subscriptions";
import SubscriptionsTable from "@/Components/App/Subscriptions/SubscriptionsTable";
import SubscriptionFormModal from "@/Components/App/Subscriptions/FormModal"
import Alert from "@/Components/Alert";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Subscriptions'}, () => page),

  components: {
    Alert,
    SubscriptionsTable,
    SearchFilterField,
    SearchFilter,
    AppPanel,
    Pagination,
    SubscriptionFormModal
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
      subscriptionFormModalOpen: false
    }
  },

  mixins: [HasFilters],

  props: {
    subscriptions: Object,
    filters: Object,
    missing_subscriptions_count: Number,
  },
}
</script>

