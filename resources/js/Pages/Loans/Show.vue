<template>
  <div class="max-w-xl mx-auto">
      <app-panel :title='`Loan #${loan.id}`'>
        <template #actions>
          <button
            @click="openFormModal = true"
            v-tippy="{ content: 'Edit Loan' }"
            class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
            edit
          </button>
        </template>
        <template #content>
          <div class="px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-2 gap-8">
              <key-value label="Member">
                <inertia-link class="hover:underline font-medium" :href="$route('members.show', loan.member.id)">
                  {{ loan.member.first_name }} {{ loan.member.last_name }}
                </inertia-link>
              </key-value>
              <key-value label="Loan date" :value="date(loan.loan_date)"/>
              <key-value label="Amount" class="font-medium">
                <money class="text-base" :amount="loan.amount"/>
              </key-value>
              <key-value label="Remaining Balance" class="font-medium">
                <div class="flex space-x-4">
                  <money class="text-base" :amount="loan.remaining_balance"/>
                  <inertia-link :href="$route('transactions.index', {loan_id: loan.id, type: TRANSACTION_TYPES['Main Transaction']})" class="flex space-x-1 items-center">
                    <span class="hover:underline text-xs font-normal">View Transactions</span>
                    <i class="material-icons-outlined text-sm">launch</i>
                  </inertia-link>
                </div>
              </key-value>
              <key-value label="Cheque Number" :value="loan.cheque_number"/>
              <key-value label="Child" :value="loan.dependent?.name || 'N/A'"/>
              <key-value label="Application Copy" v-if="loan.application_copy">
                <a class="hover:underline flex" target="_blank" :href="loan.application_copy">
                  <span class="truncate">{{ loan.application_copy }}</span>
                  <i class="material-icons-outlined ml-0.5 text-lg transform -translate-y-1">launch</i>
                </a>
              </key-value>
              <key-value label="Comments" class="col-span-2">
                <div class="font-sans whitespace-pre-wrap">{{loan.comment}}</div>
              </key-value>
              <key-value label="Guarantors" class="col-span-2">
                <div class="border rounded-md p-2">
                  <app-badge
                    v-for="guarantor in loan.guarantors"
                    color="darkGray"
                    class="m-1 cursor-pointer hover:bg-gray-800"
                    @click="$route('members.show', guarantor.id)">
                    {{ guarantor.first_name }} {{ guarantor.last_name }}
                  </app-badge>
                  <div v-show="loan.guarantors.length === 0" class="py-2 text-center text-gray-500">
                    No Guarantors
                  </div>
                </div>
              </key-value>
            </dl>
          </div>
        </template>
      </app-panel>

      <loan-form-modal
        :show="openFormModal"
        :loan="loan"
        @close="openFormModal = false"
        :dependents="loan.member.dependents"
      />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import KeyValue from "@/Components/KeyValue";
import LoanFormModal from "@/Components/App/Loans/FormModal";
import Money from "@/Components/Money";
import {date} from "@/helpers/dates";
import AppPanel from "@/Components/Panel";
import {TRANSACTION_TYPES} from '@/config/transactions';

export default {
  layout: (h, page) => h(AppLayout, {title: 'Loans'}, () => page),

  components: {
    AppPanel,
    Money,
    KeyValue,
    LoanFormModal
  },

  data() {
    return {
      openFormModal: false,
      TRANSACTION_TYPES,
    }
  },

  props: {
    loan: {
      type: Object,
      required: true
    },
  },

  methods: {
    date
  }
}
</script>
