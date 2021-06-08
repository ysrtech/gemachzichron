<template>
  <div class="max-w-4xl mx-auto grid sm:grid-cols-3 gap-6">
    <div class="col-span-2">
      <app-panel title='Loan Details'>
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
            <dl class="space-y-8">
              <key-value label="Member">
                <inertia-link class="hover:underline font-medium" :href="$route('members.show', loan.member.id)">
                  {{ loan.member.first_name }} {{ loan.member.last_name }}
                </inertia-link>
              </key-value>
              <key-value label="Loan date" :value="date(loan.loan_date)"/>
              <key-value label="Amount" class="font-medium">
                <money class="text-base" :amount="loan.amount"/>
              </key-value>
              <key-value label="Cheque Number" :value="loan.cheque_number"/>
              <key-value label="Child" :value="loan.dependent?.name || 'N/A'"/>
              <key-value label="Application Copy" v-if="loan.application_copy">
                <a class="hover:underline" target="_blank" :href="loan.application_copy">
                  {{ loan.application_copy }}
                  <i class="material-icons-outlined ml-0.5 text-lg transform translate-y-1">launch</i>
                </a>
              </key-value>
              <key-value label="Comments">
                <pre class="font-sans">{{loan.comment}}</pre>
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

    <div class="col-span-1 space-y-6">
      <app-panel title="Guarantors">
        <template #actions>
          <button
            @click="openFormModal = true"
            v-tippy="{ content: 'Edit Guarantors' }"
            class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
            edit
          </button>
        </template>
        <template #content>
          <div class="p-4 sm:px-6 flex justify-between items-start" v-for="guarantor in loan.guarantors">
            <inertia-link :href="$route('members.show', guarantor.id)" class="text-gray-700 text-sm hover:underline">
              {{ guarantor.first_name }} {{ guarantor.last_name }}
            </inertia-link>
          </div>
          <div class="px-4 py-10 sm:px-6 text-gray-400 text-center" v-show="loan.guarantors.length === 0">
            No Guarantors
          </div>
        </template>
      </app-panel>
    </div>
    <teleport v-if="isMounted" to="#header">
      Loan <span class="text-gray-500">#</span>{{ loan.id }}
    </teleport>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import KeyValue from "@/Components/UI/KeyValue";
import LoanFormModal from "@/Pages/Loans/FormModal";
import Money from "@/Components/UI/Money";
import {date} from "@/helpers/dates";
import AppPanel from "@/Components/UI/Panel";
import IsMounted from "@/Mixins/IsMounted";

export default {
  layout: AppLayout,

  components: {
    AppPanel,
    Money,
    KeyValue,
    LoanFormModal
  },

  mixins: [IsMounted],

  data() {
    return {
      openFormModal: false,
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
