<template>
  <div class="max-w-4xl mx-auto">
    <div class="space-y-4">
      <!-- Plan Types Section -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <button
          @click="planTypesExpanded = !planTypesExpanded"
          class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 focus:outline-none">
          <div class="flex items-center space-x-3">
            <i class="material-icons-outlined text-gray-600">{{ planTypesExpanded ? 'expand_less' : 'expand_more' }}</i>
            <h2 class="text-lg font-medium text-gray-900">Plan Types & Membership Rates</h2>
          </div>
        </button>

        <div v-if="planTypesExpanded" class="border-t border-gray-200">
          <div class="px-6 py-4">
            <div class="flex justify-end mb-4">
              <button
                @click="openPlanTypeFormModal()"
                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 flex items-center space-x-2">
                <i class="material-icons-outlined text-base">add</i>
                <span>Add Plan Type</span>
              </button>
            </div>

            <div class="space-y-3">
              <div v-for="planType in planTypes" :key="planType.id" class="border border-gray-200 rounded-lg">
                <!-- Plan Type Row -->
                <div class="px-6 py-3 bg-gray-50 flex items-center justify-between hover:bg-gray-100 cursor-pointer" @click="togglePlanTypeRates(planType.id)">
                  <div class="flex items-center space-x-3 flex-1">
                    <i class="material-icons-outlined text-gray-600">{{ expandedRates[planType.id] ? 'expand_less' : 'expand_more' }}</i>
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ planType.name }} <span class="text-gray-400 text-xs">(#{{ planType.id }})</span></div>
                      <div class="text-xs text-gray-500">{{ planType.members_count || 0 }} member(s)</div>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button
                      @click.stop="openPlanTypeFormModal(planType)"
                      v-tippy="{ content: 'Edit' }"
                      class="text-primary-600 hover:text-primary-900">
                      <i class="material-icons-outlined">edit</i>
                    </button>
                    <button
                      @click.stop="showDeleteModal(planType)"
                      :disabled="(planType.members_count || 0) > 0"
                      :class="[(planType.members_count || 0) > 0 ? 'text-gray-300 cursor-not-allowed' : 'text-red-600 hover:text-red-900']"
                      v-tippy="{ 
                        content: (planType.members_count || 0) > 0 
                          ? 'Cannot delete - plan type is in use' 
                          : 'Delete' 
                      }">
                      <i class="material-icons-outlined">delete</i>
                    </button>
                  </div>
                </div>

                <!-- Rates List (Collapsible) -->
                <div v-if="expandedRates[planType.id]" class="border-t border-gray-200 bg-white">
                  <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-3">
                      <h4 class="text-sm font-medium text-gray-900">Membership Rates</h4>
                      <button
                        @click="openMembershipRateFormModal(planType)"
                        class="px-2 py-1 text-xs font-medium text-white bg-primary-600 rounded hover:bg-primary-700 flex items-center space-x-1">
                        <i class="material-icons-outlined text-xs">add</i>
                        <span>Add Rate</span>
                      </button>
                    </div>

                    <div v-if="planType.rates && Object.keys(planType.rates).length > 0" class="space-y-2">
                      <div v-for="(rate, date) in planType.rates" :key="date" class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded text-sm">
                        <div>
                          <span class="font-medium text-gray-900">${{ parseFloat(rate).toFixed(2) }}</span>
                          <span class="text-gray-500 ml-2">effective {{ formatDate(date) }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                          <button
                            @click="editMembershipRate(planType, date, rate)"
                            v-tippy="{ content: 'Edit' }"
                            class="text-primary-600 hover:text-primary-900">
                            <i class="material-icons-outlined text-sm">edit</i>
                          </button>
                          <button
                            @click="deleteMembershipRate(planType, date)"
                            v-tippy="{ content: 'Delete' }"
                            class="text-red-600 hover:text-red-900">
                            <i class="material-icons-outlined text-sm">delete</i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div v-else class="text-sm text-gray-500 text-center py-2">
                      No rates configured
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="planTypes.length === 0" class="text-center text-gray-500 py-4">
                No plan types found.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <plan-type-form-modal
      :show="showPlanTypeFormModal"
      :plan-type="selectedPlanType"
      @close="showPlanTypeFormModal = false; selectedPlanType = null"
    />

    <confirm-modal
      :show="showDeleteConfirmModal"
      title="Delete Plan Type"
      message="Are you sure you want to delete this plan type? This action cannot be undone."
      confirm-text="Delete"
      confirm-color="red"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirmModal = false"
    />

    <membership-rate-form-modal
      :show="showMembershipRateFormModal"
      :plan-type="selectedPlanTypeForRate"
      :rate="selectedRate"
      :date="selectedRateDate"
      @close="showMembershipRateFormModal = false; selectedPlanTypeForRate = null; selectedRate = null; selectedRateDate = null"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import PlanTypeFormModal from "./PlanTypeFormModal";
import ConfirmModal from "@/Components/ConfirmModal";
import MembershipRateFormModal from "./MembershipRateFormModal";

export default {
  layout: (h, page) => h(AppLayout, { title: "Settings" }, () => page),

  components: {
    PlanTypeFormModal,
    ConfirmModal,
    MembershipRateFormModal,
  },

  data() {
    return {
      planTypesExpanded: true,
      expandedRates: {},
      showPlanTypeFormModal: false,
      selectedPlanType: null,
      showDeleteConfirmModal: false,
      planTypeToDelete: null,
      showMembershipRateFormModal: false,
      selectedPlanTypeForRate: null,
      selectedRate: null,
      selectedRateDate: null,
    }
  },

  props: {
    planTypes: Array,
  },

  methods: {
    togglePlanTypeRates(planTypeId) {
      this.expandedRates[planTypeId] = !this.expandedRates[planTypeId];
      this.$forceUpdate();
    },

    openPlanTypeFormModal(planType = null) {
      this.selectedPlanType = planType;
      this.showPlanTypeFormModal = true;
    },

    showDeleteModal(planType) {
      this.planTypeToDelete = planType;
      this.showDeleteConfirmModal = true;
    },

    confirmDelete() {
      this.$inertia.delete(this.$route('settings.plan-types.destroy', this.planTypeToDelete.id));
      this.showDeleteConfirmModal = false;
    },

    openMembershipRateFormModal(planType) {
      this.selectedPlanTypeForRate = planType;
      this.selectedRate = null;
      this.selectedRateDate = null;
      this.showMembershipRateFormModal = true;
    },

    editMembershipRate(planType, date, rate) {
      this.selectedPlanTypeForRate = planType;
      this.selectedRate = rate;
      this.selectedRateDate = date;
      this.showMembershipRateFormModal = true;
    },

    deleteMembershipRate(planType, date) {
      if (confirm('Delete this rate?')) {
        // We'll need to add a delete endpoint - for now just show a message
        this.$inertia.delete(this.$route('settings.plan-types.rate.delete', { planType: planType.id, date }));
      }
    },

    formatDate(dateString) {
      return new Date(dateString + 'T00:00:00').toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }
  }
}
</script>
