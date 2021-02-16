<template>
  <div class="w-full">

    <div class="flex space-x-1 items-center">
      <h3 class="text-xl font-medium leading-6 text-gray-900 mx-1 py-1.5">Loans</h3>
      <button
        title="Add Loan"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
        @click="openLoanModal = true">
        add
      </button>
    </div>

    <div class="bg-white rounded-lg shadow w-full mt-2 mx-1 overflow-x-auto overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead v-if="member.membership.loans.length > 0">

          <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
            <th class="px-6 py-3 font-medium" v-for="title in ['Dependent',	'Amount',	'Loan date',	'Cheque Number', '']">{{ title }}</th>
          </tr>

        </thead>
        <tbody class="bg-white divide-y divide-gray-200">

          <tr
            v-for="loan in member.membership.loans"
            :key="loan.id"
            @click="loanToEdit = loan; openLoanModal = true"
            class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer"
          >
            <td class="px-6 py-3.5 whitespace-nowrap">
              {{ dependentById(loan.dependent_id)?.first_name + ' ' + dependentById(loan.dependent_id)?.last_name }}
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">${{ loan.amount }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ date(loan.loan_date) }}</td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ loan.cheque_number }}</td>
            <td class="pr-5 text-center w-px whitespace-nowrap text-gray-500 cursor-default" @click.stop>
              <a
                v-if="loan.application_copy"
                :href="loan.application_copy"
                target="_blank"
                title="View Application Form"
                class="material-icons-outlined focus:outline-none rounded-full text-lg py-1 px-2 hover:bg-gray-200 focus:bg-gray-300">
                file_copy
              </a>
            </td>
          </tr>

          <tr v-if="member.membership.loans.length === 0">
            <td class="px-6 py-10 text-center text-gray-500">No loans.</td>
          </tr>

        </tbody>
      </table>
    </div>

    <loan-modal
      :show="openLoanModal"
      :loan="loanToEdit"
      :membershipId="member.id"
      :dependents="member.dependents"
      @close="openLoanModal = false; loanToEdit = null"
    />

  </div>
</template>

<script>
import LoanModal from "@/Pages/Members/Partials/LoanModal";
import {date} from "@/helpers/dates";
import {find} from "lodash";

export default {

  components: {
    LoanModal,
  },

  data() {
    return {
      openLoanModal: false,
      loanToEdit: null,
    }
  },

  props: {
    member: Object,
  },

  methods: {
    date,
    dependentById(id) {
      return find(this.member.dependents, {id:id})
    },
  }
}
</script>
