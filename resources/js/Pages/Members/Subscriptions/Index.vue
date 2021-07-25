<template>
  <div>
    <member-base :member="member">
      <div class="max-w-6xl mx-auto">
        <app-panel title="Subscriptions">
          <template #actions>
            <button
              @click="openFormModal = true"
              v-tippy="{ content: 'Add New Subscription' }"
              class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
              add
            </button>
          </template>
          <template #content>
            <subscriptions-table :subscriptions="member.subscriptions"/>
          </template>
        </app-panel>
      </div>
      <subscriptions-form-modal
        :show="openFormModal"
        :member="member"
        @close="openFormModal = false"
      />
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
    }
  },

  props: {
    member: Object,
  },
}
</script>
