<template>
  <div>
    <member-base :member="member">
      <div class="max-w-5xl mx-auto grid">
              <app-panel
      title="Membership"
      :badge="member.active_membership === false ? {color: 'red', text: 'Inactive'} : null">
      <template #content>
        <template v-if="member.membership_since">
          <div class="px-4 py-5 sm:px-6 grid">
            <dl class="space-y-8">
              <key-value label="Membership Since" :value="date(member.membership_since)"/>
              <key-value label="Membership Type">
                {{ member.membership_type }} <span v-if="member.plan_type">({{ member.plan_type.name }})</span>
              </key-value>
            </dl>
          </div>
          <div class="px-4 py-5 sm:px-6 flex items-center">
            <div class="font-medium text-gray-400 flex-1" title="Excluding Fees">Total Paid <span class="text-xs font-normal">(Membership)</span></div>
            <div class="text-2xl">
              <money
                :amount="member.membership_payments_total || 0"
                class="font-medium text-gray-900 text-right"
                currency-sign-class="font-normal text-gray-600 mr-1"/>
            </div>
          </div>
        </template>

        <div v-else class="py-10 text-center text-gray-500">
          <div class="pb-4 px-6 text-sm">Member does not have a membership</div>
        </div>
      </template>
    </app-panel>
    <div class="mt-10">
     <app-panel title="Loans">
         
          <template #content>
            <table class="min-w-full divide-y divide-gray-200">
              <thead v-if="member.loans.length > 0">
              <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
                <th class="px-6 py-3 font-medium">ID</th>
                <th class="px-6 py-3 font-medium">Loan Date</th>
                <th class="px-6 py-3 font-medium text-right">Amount</th>
                <th class="px-6 py-3 font-medium text-right">Remaining Balance</th>
              </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
              <tr
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
  </div>
  <div class="mt-10">
        <app-panel title="Guarantees" description="Given Guarantees">
          <template #content>
            <table class="min-w-full divide-y divide-gray-200 mt-2">
              <thead v-if="member.guarantees.length > 0">
            
              <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
                 <th class="px-6 py-3 font-medium">Loan ID</th>
                <th class="px-6 py-3 font-medium">Member</th>
                <th class="px-6 py-3 font-medium">Loan date</th>
                <th class="px-6 py-3 font-medium text-right">Amount</th>
                <th class="px-6 py-3 font-medium text-right">Remaining Balance</th>
              </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="loan in member.guarantees"
                :key="loan.id"
                class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
                <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ loan.id }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap space-x-2 font-medium">
                  {{ loan.member.first_name + ' ' + loan.member.last_name }}
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap font-medium text-right">
                  <money :amount="loan.amount"/>
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap font-medium text-right ">
                  <money :amount="loan.remaining_balance"/>
                </td>
              </tr>

              <tr v-if="member.guarantees.length > 0" class="text-sm bg-gray-50">

                <td colspan="3"  class="px-6 py-3.5 font-medium uppercase border-t border-gray-300">Totals:</td>
                <td class="px-6 py-3.5 font-semibold border-t border-gray-300 text-right">
                  <money :amount="member.guarantees.reduce((sum, loan) => sum + loan.amount, 0)"/>
                </td>
                <td class="px-6 py-3.5 font-semibold border-t border-gray-300 text-right">
                  <money :amount="member.guarantees.reduce((sum, loan) => sum + loan.remaining_balance, 0)"/>
                </td>
                
              </tr>

              <tr v-if="member.guarantees.length === 0">
                <td class="px-6 py-10 text-center text-gray-500" colspan="3">No Guarantees Given.</td>
              </tr>

              </tbody>
            </table>
          </template>
        </app-panel>
</div>
      </div>
    </member-base>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import MemberBase from "@/Pages/Members/MemberBase";
import {date} from "@/helpers/dates";
import KeyValue from "@/Components/KeyValue";
import Money from "@/Components/Money";
import AppPanel from "@/Components/Panel";

export default {
  components: {
    AppPanel,
    Money,
    KeyValue,
    MemberBase
  },

  layout: AppLayout,

  props: {
    member: Object,
  },
   methods: {
    date,
  }
}
</script>
