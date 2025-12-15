<template>
  <div>
    <member-base :member="member">
      <div class="max-w-4xl mx-auto space-y-6">
        <!-- Loans Section -->
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
                <th class="px-6 py-3 font-medium">ID</th>
                <th class="px-6 py-3 font-medium">Loan Date</th>
                <th class="px-6 py-3 font-medium text-right">Amount</th>
                <th class="px-6 py-3 font-medium text-right">Remaining Balance</th>
                <th class="px-6 py-3 font-medium">Cheque #</th>
                <th class="px-6 py-3 font-medium"></th>
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
                <td class="px-6 py-3.5 whitespace-nowrap font-medium text-right">
                  <money :amount="loan.amount"/>
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap text-right">
                  <money :amount="loan.remaining_balance"/>
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

              <tr v-if="member.loans.length > 0" class="text-sm bg-gray-50">
                <td colspan="2" class="px-6 py-3.5 font-medium uppercase border-t border-gray-300">Total:</td>
                <td class="px-6 py-3.5 font-semibold text-right border-t border-gray-300">
                  <money :amount="member.loans.reduce((sum, loan) => sum + loan.amount, 0)"/>
                </td>
                <td class="px-6 py-3.5 font-semibold text-right border-t border-gray-300">
                  <money :amount="member.loans.reduce((sum, loan) => sum + loan.remaining_balance, 0)"/>
                </td>
                <td class="border-t border-gray-300"></td>
                <td class="border-t border-gray-300"></td>
              </tr>

              <tr v-else>
                <td class="px-6 py-10 text-center text-gray-500" colspan="5">No Loans Found.</td>
              </tr>

              </tbody>
            </table>

          </template>
        </app-panel>

        <!-- Withdrawals Section -->
        <app-panel title="Withdrawals">
          <template #actions>
            <button
              @click="openWithdrawalFormModal = true"
              v-tippy="{ content: 'Add New Withdrawal' }"
              class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
              add
            </button>
          </template>
          <template #content>
            <table class="min-w-full divide-y divide-gray-200">
              <thead v-if="member.withdrawals && member.withdrawals.length > 0">
              <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
                <th class="px-6 py-3 font-medium">ID</th>
                <th class="px-6 py-3 font-medium">Withdrawal Date</th>
                <th class="px-6 py-3 font-medium text-right">Amount</th>
                <th class="px-6 py-3 font-medium">Method</th>
                <th class="px-6 py-3 font-medium"></th>
              </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="withdrawal in member.withdrawals"
                :key="withdrawal.id"
                class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ withdrawal.id }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap">{{ date(withdrawal.withdrawal_date) }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap font-medium text-right">
                  <money :amount="withdrawal.amount"/>
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap">{{ withdrawal.method }}</td>
                <td @click.stop class="px-5 text-right whitespace-nowrap text-gray-500 space-x-2 cursor-default">
                  <button
                    @click="openWithdrawalFormModal = true; withdrawalToEdit = withdrawal"
                    class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                    edit
                  </button>
                </td>
              </tr>

              <tr v-if="member.withdrawals && member.withdrawals.length > 0" class="text-sm bg-gray-50">
                <td colspan="2" class="px-6 py-3.5 font-medium uppercase border-t border-gray-300">Total:</td>
                <td class="px-6 py-3.5 font-semibold text-right border-t border-gray-300">
                  <money :amount="member.withdrawals.reduce((sum, withdrawal) => sum + withdrawal.amount, 0)"/>
                </td>
                <td class="border-t border-gray-300" colspan="2"></td>
              </tr>

              <tr v-else>
                <td class="px-6 py-10 text-center text-gray-500" colspan="5">No Withdrawals Found.</td>
              </tr>

              </tbody>
            </table>

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
      <withdrawals-form-modal
        :show="openWithdrawalFormModal"
        :member-id="member.id"
        :withdrawal="withdrawalToEdit"
        @close="openWithdrawalFormModal = false; withdrawalToEdit = null"
      />
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import LoansFormModal from "@/Components/App/Loans/FormModal";
import WithdrawalsFormModal from "@/Components/App/Withdrawals/FormModal";
import AppPanel from "@/Components/Panel";
import Money from "@/Components/Money";

export default {
  layout: AppLayout,

  components: {
    Money,
    AppPanel,
    LoansFormModal,
    WithdrawalsFormModal,
    MemberBase
  },

  data() {
    return {
      loanToEdit: null,
      openFormModal: false,
      withdrawalToEdit: null,
      openWithdrawalFormModal: false
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
