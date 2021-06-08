<template>
  <div>
    <member-base :member="member">
      <div class="max-w-3xl mx-auto">
        <app-panel title="Loans">
          <template #actions>
            <button
              @click="openFormModal = true"
              v-tippy="{ content: 'Add New Loan' }"
              class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
              add
            </button>
          </template>
          <template #content>
            <table class="min-w-full divide-y divide-gray-200">
              <thead v-if="member.loans.length > 0">
              <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
                <th v-for="title in ['ID', 'Loan date', 'Amount', 'Cheque Number', '']" class="px-6 py-3 font-medium">
                  {{ title }}
                </th>
              </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
              <tr
                @click="$inertia.get($route('loans.show', loan.id))"
                v-for="loan in member.loans"
                :key="loan.id"
                class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
                <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ loan.id }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap font-medium">
                  <money :amount="loan.amount"/>
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap">{{ loan.cheque_number }}</td>
                <td @click.stop class="px-5 text-right whitespace-nowrap text-gray-500 space-x-2 cursor-default">
                  <button
                    @click="openFormModal = true; loanToEdit = loan"
                    class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                    edit
                  </button>
                </td>
              </tr>

              <tr v-if="member.loans.length === 0">
                <td class="px-6 py-10 text-center text-gray-500" colspan="5">No Loans Found.</td>
              </tr>

              </tbody>
            </table>
            <hr>
            <div v-if="member.loans.length > 0" class="px-6 py-5 text-gray-500 w-full flex">
              <span class="flex-1 flex space-x-2">
                <span class="font-medium" >Total:</span>
                <money :amount="member.loans_sum_amount"/>
              </span>
              <span class="flex-1 flex space-x-2" title="Exluding Fees">
                <span class="font-medium" >Total Paid:</span>
                <money :amount="member.loan_payments_total"/>
              </span>
            </div>

          </template>
        </app-panel>
      </div>
      <loans-form-modal
        :show="openFormModal"
        :member-id="member.id"
        :dependents="member.dependents"
        :loan="loanToEdit"
        @close="openFormModal = false; loanToEdit = null"
      />
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import LoansFormModal from "@/Pages/Loans/FormModal";
import AppPanel from "@/Components/UI/Panel";
import Money from "@/Components/UI/Money";

export default {
  layout: AppLayout,

  components: {
    Money,
    AppPanel,
    LoansFormModal,
    MemberBase
  },

  data() {
    return {
      loanToEdit: null,
      openFormModal: false
    }
  },

  props: {
    member: Object,
  },

  methods: {
    date,
  }
}
</script>
