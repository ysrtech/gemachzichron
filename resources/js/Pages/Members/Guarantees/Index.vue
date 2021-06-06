<template>
  <div>
    <member-base :member="member">
      <div class="max-w-3xl mx-auto">
        <app-panel title="Guarantees" description="Given Guarantees">
          <template #content>
            <table class="min-w-full divide-y divide-gray-200">
              <thead v-if="member.guarantees.length > 0">
              <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
                <th v-for="title in ['Loan ID', 'Member', 'Loan date', 'Amount']" class="px-6 py-3 font-medium">{{title}}</th>
              </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
              <tr
                @click="$inertia.get($route('loans.show', loan.id))"
                v-for="loan in member.guarantees"
                :key="loan.id"
                class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
                <td class="px-6 py-3.5 whitespace-nowrap space-x-2">{{ loan.id }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap space-x-2 font-medium">
                  {{ loan.member.first_name + ' ' + loan.member.last_name }}
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
                <td class="px-6 py-3.5 whitespace-nowrap font-medium">
                  <money :amount="loan.amount"/>
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
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/PersistentAppLayout";
import {date} from "@/helpers/dates";
import Money from "@/Components/UI/Money";
import AppPanel from "@/Components/UI/Panel";

export default {
  layout: AppLayout,

  components: {
    AppPanel,
    Money,
    MemberBase
  },

  props: {
    member: Object,
  },

  methods: {
    date,
  }
}
</script>
