<template>
  <div class="bg-white shadow rounded-md px-6 pb-4">

    <div class="py-5 flex items-center justify-between">
      <div class="text-xl font-medium">Loans</div>
      <button
        class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
        @click="createLoan = true">
        add
      </button>
    </div>

    <div class="bg-white rounded border border-gray-200 overflow-x-auto">
      <table class="w-full whitespace-no-wrap">

        <tr class="text-left text-xs text-gray-400 uppercase">
          <th class="px-6 py-3 font-medium">Dependent</th>
          <th class="px-6 py-3 font-medium">Amount</th>
          <th class="px-6 py-3 font-medium">Loan Date</th>
          <th class="px-6 py-3 font-medium">Cheque Number</th>
          <th class="px-6 py-3 font-medium"></th>
        </tr>

        <tr v-for="loan in loans" :key="loan.id">
          <td class="border-t px-6 py-3">
            {{ dependentById(loan.dependent_id).first_name + ' ' + dependentById(loan.dependent_id).last_name }}
          </td>
          <td class="border-t px-6 py-3">${{ loan.amount }}</td>
          <td class="border-t px-6 py-3">{{ formatDate(loan.loan_date) }}</td>
          <td class="border-t px-6 py-3">{{ loan.cheque_number }}</td>
          <td class="border-t w-px px-6 py-0 text-gray-500">
            <button
              class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
              @click="loanToEdit = loan">
              edit
            </button>
            <button
              v-if="loan.application_copy"
              class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
              title="View Application"
              @click="openInNewTab(loan.application_copy)">
              open_in_new
            </button>
          </td>

        </tr>

        <tr v-if="loans.length === 0">
          <td class="border-t px-6 py-4" colspan="5">No loans.</td>
        </tr>

      </table>
    </div>

    <loan-form-modal
      :loan="loanToEdit"
      :show="createLoan"
      :membership-id="membershipId"
      :dependents="dependents"
      @close="loanToEdit = null;createLoan = false;"
    />


  </div>
</template>

<script>
import LoanFormModal from "./LoanFormModal";

export default {
  components: {
    LoanFormModal
  },

  data() {
    return {
      loanToEdit: null,
      createLoan: false
    }
  },

  props: {
    loans: Array,
    dependents: Array,
    membershipId: Number
  },


  methods: {
    dependentById(id) {
      return _.find(this.dependents, {id:id})
    },

    openInNewTab(url) {
      window.open(url);
    }
  }

}
</script>
