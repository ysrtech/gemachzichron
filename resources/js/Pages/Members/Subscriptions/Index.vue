<template>
  <div>
    <member-base :member="member">
      <div class="max-w-12xl mx-auto">
        <app-panel title="Subscriptions">
          <template #actions>
            <div class="flex items-center space-x-2">
              <button
                @click="openFormModal = true"
                class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-700 disabled:opacity-25 transition">
                New Subscription
              </button>
              <div class="relative">
                <button
                  @click="showActionsMenu = !showActionsMenu"
                  class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 transition">
                  Actions
                  <span class="material-icons-outlined text-sm ml-1">expand_more</span>
                </button>
                <div
                  v-if="showActionsMenu"
                  class="absolute right-0 mt-1 w-56 bg-white border border-gray-200 rounded shadow-lg z-10">
                  <button
                    @click="confirmAdjustLoanPayments"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Adjust Loan Payments
                  </button>
                </div>
              </div>
            </div>
          </template>
          <template #content>
            <subscriptions-table :subscriptions="member.subscriptions"/>
          </template>
        </app-panel>
      </div>
      <subscriptions-form-modal
        :show="openFormModal"
        :member-prop="member"
        @close="openFormModal = false"
      />

      <!-- Confirmation Modal -->
      <div
        v-if="showConfirmationModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click.self="showConfirmationModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-3">
            Adjust Loan Payments
          </h3>
          
          <div v-if="loadingPreview" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-700"></div>
            <p class="mt-2 text-gray-600">Loading...</p>
          </div>

          <div v-else-if="subscriptionPreview">
            <div v-if="subscriptionPreview.canAdjust">
              <p class="text-gray-600 mb-4">
                The following subscription will be adjusted:
              </p>
              
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Subscription ID:</span>
                    <span class="text-sm text-gray-900">#{{ subscriptionPreview.subscription.id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Current Amount:</span>
                    <span class="text-sm text-gray-900">${{ subscriptionPreview.subscription.amount }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">New Amount:</span>
                    <span class="text-sm font-semibold text-green-600">${{ subscriptionPreview.subscription.new_amount }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Frequency:</span>
                    <span class="text-sm text-gray-900">{{ subscriptionPreview.subscription.frequency }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Gateway:</span>
                    <span class="text-sm text-gray-900">{{ subscriptionPreview.subscription.gateway }}</span>
                  </div>
                </div>
              </div>
              
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm font-medium text-gray-900 mb-3">What will happen:</p>
                <div class="space-y-2">
                  <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Subscription #{{ subscriptionPreview.subscription.id }} will be adjusted to $350</span>
                  </div>
                  <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Email confirmation will be sent to {{ member.email || 'member' }}</span>
                  </div>
                </div>
              </div>
              
              <div class="flex justify-end space-x-3">
                <button
                  @click="showConfirmationModal = false"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 transition">
                  Cancel
                </button>
                <button
                  @click="adjustLoanPayments"
                  :disabled="processing"
                  class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-700 disabled:opacity-25 transition">
                  {{ processing ? 'Processing...' : 'Confirm Adjustment' }}
                </button>
              </div>
            </div>
            
            <div v-else>
              <p class="text-gray-600 mb-6">
                {{ subscriptionPreview.message }}
              </p>
              <div class="flex justify-end">
                <button
                  @click="showConfirmationModal = false"
                  class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-700 transition">
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import SubscriptionsFormModal from "@/Components/App/Subscriptions/FormModal";
import AppPanel from "@/Components/Panel";
import SubscriptionsTable from "@/Components/App/Subscriptions/SubscriptionsTable";

export default {
  layout: AppLayout,

  components: {
    SubscriptionsTable,
    AppPanel,
    SubscriptionsFormModal,
    MemberBase
  },

  data() {
    return {
      openFormModal: false,
      showActionsMenu: false,
      showConfirmationModal: false,
      processing: false,
      subscriptionPreview: null,
      loadingPreview: false,
    }
  },

  props: {
    member: Object,
  },

  methods: {
    confirmAdjustLoanPayments() {
      this.showActionsMenu = false;
      this.loadingPreview = true;
      this.showConfirmationModal = true;
      this.subscriptionPreview = null;

      // Fetch preview of what would be adjusted
      fetch(this.$route('members.subscriptions.preview-adjust-loan-payments', this.member.id))
        .then(response => response.json())
        .then(data => {
          this.subscriptionPreview = data;
          this.loadingPreview = false;
        })
        .catch(() => {
          this.loadingPreview = false;
          this.showConfirmationModal = false;
          alert('Failed to load adjustment preview');
        });
    },

    adjustLoanPayments() {
      this.processing = true;
      this.$inertia.post(
        this.$route('members.subscriptions.adjust-loan-payments', this.member.id),
        {},
        {
          onFinish: () => {
            this.processing = false;
            this.showConfirmationModal = false;
          },
        }
      );
    },
  },

  mounted() {
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.relative')) {
        this.showActionsMenu = false;
      }
    });
  },
}
</script>
