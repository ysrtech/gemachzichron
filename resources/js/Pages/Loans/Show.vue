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
              <key-value label="Guarantors">
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
