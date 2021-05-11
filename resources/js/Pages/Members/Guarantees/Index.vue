<template>
  <div>
    <member-base :member="member">
      <div class="max-w-3xl mx-auto bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden">
        <div class="p-4 sm:px-6 flex items-center justify-between">
          <div>
            <h2 class="text-lg leading-6 font-medium text-gray-900">
              Guarantees
            </h2>
            <p class="mt-1 text-sm">Given Guarantees</p>
          </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
          <thead v-if="member.guarantees.length > 0">
          <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
            <th v-for="title in ['ID', 'Member', 'Loan date', 'Amount']" class="px-6 py-3 font-medium">{{ title }}</th>
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
              {{ loan.membership.member.first_name + ' ' + loan.membership.member.last_name }}
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap font-medium"><span class="mr-1 text-gray-500">$</span>{{ Number(loan.amount).toFixed(2) }}</td>
          </tr>

          <tr v-if="member.guarantees.length === 0">
            <td class="px-6 py-10 text-center text-gray-500" colspan="3">No Guarantees Found.</td>
          </tr>

          </tbody>
        </table>
      </div>

    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";

export default {
  layout: AppLayout,

  components: {
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
