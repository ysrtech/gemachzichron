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

      <div class="flex items-center space-x-2">
        <app-button @click="subscriptionFormModalOpen = true">New Subscription</app-button>
        
        <div class="relative">
          <button
            @click="showActionsMenu = !showActionsMenu"
            class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 transition">
            Actions
            <span class="material-icons-outlined text-sm ml-1">expand_more</span>
          </button>
          <div
            v-if="showActionsMenu"
            class="absolute right-0 mt-1 w-64 bg-white border border-gray-200 rounded shadow-lg z-10">
            <button
              @click="previewBulkAdjustment"
              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Bulk Adjust Loan Payments
            </button>
          </div>
        </div>
      </div>
    </div>

    <app-panel class="overflow-x-auto">
      <template #content>
        <subscriptions-table :subscriptions="subscriptions.data" show-member v-model:sort="filterForm.sort"/>

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

    <!-- Bulk Adjustment Modal -->
    <div
      v-if="showAdjustmentModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click.self="closeAdjustmentModal">
      <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Bulk Adjust Loan Payments
        </h3>
        
        <div v-if="loadingPreview" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-700"></div>
          <p class="mt-2 text-gray-600">Loading preview...</p>
        </div>

        <div v-else-if="adjustmentPreview">
          <div v-if="adjustmentPreview.count > 0">
            <p class="text-gray-600 mb-4">
              Found <strong>{{ adjustmentPreview.count }}</strong> subscription(s) that will be adjusted:
            </p>
            
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 max-h-96 overflow-y-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 sticky top-0">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Member</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Subscription</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Current</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">New</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Gateway</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="adj in adjustmentPreview.adjustments" :key="adj.subscription_id" class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-sm text-gray-900">{{ adj.member_name }}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">#{{ adj.subscription_id }}</td>
                    <td class="px-3 py-2 text-sm text-gray-900">${{ adj.current_amount }}</td>
                    <td class="px-3 py-2 text-sm font-semibold text-green-600">${{ adj.new_amount }}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">{{ adj.gateway }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
              <p class="text-sm font-medium text-gray-900 mb-3">What will happen:</p>
              <div class="space-y-2">
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="text-sm text-gray-700">{{ adjustmentPreview.count }} subscription(s) will be adjusted to $350</span>
                </div>
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="text-sm text-gray-700">Email confirmations will be sent to each member</span>
                </div>
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="text-sm text-gray-700">Gateway subscriptions will be updated automatically</span>
                </div>
              </div>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button
                @click="closeAdjustmentModal"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 transition">
                Cancel
              </button>
              <button
                @click="executeBulkAdjustment"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-700 disabled:opacity-25 transition">
                {{ processing ? 'Processing...' : 'Confirm Bulk Adjustment' }}
              </button>
            </div>
          </div>
          
          <div v-else>
            <p class="text-gray-600 mb-6">
              No subscriptions found that qualify for adjustment.
            </p>
            <div class="flex justify-end">
              <button
                @click="closeAdjustmentModal"
                class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-700 transition">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
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
        sort: this.filters.sort,
        search: this.filters.search,
        amount: this.filters.amount,
        type: this.filters.type,
        active: this.filters.active,
        gateway: this.filters.gateway,
      },
      AVAILABLE_GATEWAYS,
      SUBSCRIPTION_TYPES,
      subscriptionFormModalOpen: false,
      showActionsMenu: false,
      showAdjustmentModal: false,
      loadingPreview: false,
      adjustmentPreview: null,
      processing: false,
    }
  },

  mixins: [HasFilters],

  props: {
    subscriptions: Object,
    filters: Object,
    missing_subscriptions_count: Number,
  },

  methods: {
    previewBulkAdjustment() {
      this.showActionsMenu = false;
      this.loadingPreview = true;
      this.showAdjustmentModal = true;
      this.adjustmentPreview = null;

      fetch(this.$route('subscriptions.preview-bulk-adjust'))
        .then(response => response.json())
        .then(data => {
          this.adjustmentPreview = data;
          this.loadingPreview = false;
        })
        .catch(() => {
          this.loadingPreview = false;
          this.showAdjustmentModal = false;
          alert('Failed to load adjustment preview');
        });
    },

    executeBulkAdjustment() {
      this.processing = true;
      this.$inertia.post(
        this.$route('subscriptions.execute-bulk-adjust'),
        {},
        {
          onFinish: () => {
            this.processing = false;
            this.showAdjustmentModal = false;
          },
        }
      );
    },

    closeAdjustmentModal() {
      this.showAdjustmentModal = false;
      this.adjustmentPreview = null;
    },
  },

  mounted() {
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.relative')) {
        this.showActionsMenu = false;
      }
    });
  },
}
</script>

