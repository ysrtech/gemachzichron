<template>
  <div>
    <member-base :member="member">
      <div class="max-w-3xl mx-auto">
        <app-panel title="Payment Methods">
          <template #actions>
            <button
              @click="openFormModal = true"
              v-tippy="{ content: 'Add New Payment Method' }"
              class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
              add
            </button>
          </template>
          <template #content>
            <div class="p-6 grid grid-cols-3" v-if="member.payment_methods.length > 0">
              <app-panel class="bg-gray-50" v-for="paymentMethod in member.payment_methods" :title="paymentMethod.gateway">
                <template #actions>
                  <button
                    @click="openFormModal = true; paymentMethodToEdit = paymentMethod"
                    v-tippy="{ content: 'Edit Payment Method' }"
                    class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
                    edit
                  </button>
                </template>
                <template #content>
                  <div class="px-4 py-5 sm:px-6">
                    <dl class="space-y-4">
                      <template v-for="(data, key) in paymentMethod.gateway_data">
                        <key-value v-if="data" :label="key.replace('_', ' ')" :value="data"/>
                      </template>
                    </dl>
                  </div>
                </template>
              </app-panel>
            </div>
            <div v-else class="px-6 py-10 text-center text-gray-500">
              No Payment Methods Found.
            </div>
          </template>
        </app-panel>
      </div>
      <payment-methods-form-modal
        :show="openFormModal"
        :paymentMethod="paymentMethodToEdit"
        :member="member"
        @close="openFormModal = false; paymentMethodToEdit = null"
      />
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import PaymentMethodsFormModal from "./FormModal";
import AppPanel from "@/Components/UI/Panel";
import Money from "@/Components/UI/Money";
import KeyValue from "@/Components/UI/KeyValue";

export default {
  layout: AppLayout,

  components: {
    KeyValue,
    Money,
    AppPanel,
    PaymentMethodsFormModal,
    MemberBase
  },

  data() {
    return {
      paymentMethodToEdit: null,
      openFormModal: false
    }
  },

  props: {
    member: Object,
  },
}
</script>
