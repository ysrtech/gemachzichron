<template>
  <div class="max-w-4xl mx-auto grid sm:grid-cols-3 gap-6">
    <div class="col-span-2">
      <div class="bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden">
        <div class="p-4 sm:px-6 flex items-center justify-between">
          <div class="flex space-x-2 items-baseline">
            <h2 class="text-lg leading-6 font-medium text-gray-900">Loan <span class="text-gray-700">#</span>{{ loan.id }}</h2>
          </div>
          <button
            @click="openFormModal = true"
            title="Edit Loan"
            class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
            edit
          </button>
        </div>
        <div class="px-4 py-5 sm:px-6">
          <dl class="space-y-8">
            <key-value label="Member">
              <inertia-link class="hover:underline font-medium" :href="$route('members.show', loan.membership.member.id)">
                {{ loan.membership.member.first_name }} {{ loan.membership.member.last_name }}
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
          </dl>
        </div>

        <loan-form-modal
          :show="openFormModal"
          :loan="loan"
          @close="openFormModal = false"
          :dependents="loan.membership.member.dependents"
        />
      </div>
    </div>
    <div class="col-span-1 space-y-6">
      <div class="bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden">
        <div class="p-4 sm:px-6 flex items-center justify-between">
          <h2 class="text-lg leading-6 font-medium text-gray-900">Guarantors</h2>
          <button
            @click="openFormModal = true"
            title="Edit Guarantors"
            class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
            edit
          </button>
        </div>
        <div class="p-4 sm:px-6 flex justify-between items-start" v-for="guarantor in loan.guarantors">
          <inertia-link :href="$route('members.show', guarantor.id)" class="text-gray-700 text-sm hover:underline">
            {{ guarantor.first_name }} {{ guarantor.last_name }}
          </inertia-link>
        </div>
        <div class="px-4 py-10 sm:px-6 text-gray-400 text-center" v-show="loan.guarantors.length === 0">
          No Guarantors
        </div>
      </div>
      <comments-section
        commentable-type="loan"
        :commentable-id="loan.id"
        :comments="loan.comments"
      />
    </div>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import KeyValue from "@/Components/UI/KeyValue";
import LoanFormModal from "@/Pages/Loans/FormModal";
import CommentsSection from "@/Components/App/CommentsSection";
import Money from "@/Components/UI/Money";
import {date} from "@/helpers/dates";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Loans'}, () => page),

  components: {
    Money,
    CommentsSection,
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
