<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')" class="overflow-visible">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="loan">Edit Loan</template>
      <template v-else>Add New Loan</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 pb-4 grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <app-input
          id="type"
          v-model="form.dependent_id"
          :error="form.errors.dependent_id"
          label="Dependent"
          type="select"
          @input="form.clearErrors('dependent_id')">
          <template #options>
            <option :value="null">--</option>
            <option
              v-for="dependent in dependents"
              :key="dependent.id"
              :value="dependent.id">
              {{ dependent.name }}
            </option>
          </template>
        </app-input>

        <app-input
          id="amount"
          v-model="form.amount"
          :error="form.errors.amount"
          label="Amount"
          type="number"
          @input="form.clearErrors('amount')"
        />

        <app-input
          id="loan_date"
          v-model="form.loan_date"
          :error="form.errors.loan_date"
          label="Loan Date"
          type="date"
          @input="form.clearErrors('loan_date')"
        />

        <app-input
          id="cheque_number"
          v-model="form.cheque_number"
          :error="form.errors.cheque_number"
          label="Cheque Number"
          type="text"
          @input="form.clearErrors('cheque_number')"
        />

        <div class="col-span-2">
          <app-input
            id="application_copy"
            v-model="form.application_copy"
            :error="form.errors.application_copy"
            label="Application Copy"
            type="file"
            :filename="applicationCopyPreview"
            @input="form.clearErrors('application_copy')"
          >
            <template #top-right>
              <a
                v-if="loan?.application_copy"
                :href="loan.application_copy"
                target="_blank"
                class="text-xs text-gray-500 hover:text-gray-800 flex items-center">
                <span class="mr-1">View</span>
                <i class="material-icons-outlined text-base">launch</i>
              </a>
            </template>
          </app-input>
        </div>

        <div class="col-span-2">
          <app-input
            id="comment"
            v-model="form.comment"
            :error="form.errors.comment"
            label="Comments"
            type="textarea"
            @input="form.clearErrors('comment')"
          />
        </div>

        <div class="col-span-2 relative">
          <app-dropdown width="full" align="left" :close-on-click="false">
            <template #trigger>
              <app-input label="Guarantors" type="div" class="space-x-1 inline-flex flex-wrap items-center" :error="form.errors.guarantors">
                <app-badge v-for="guarantor in form.guarantors" class="inline-flex items-center">
                  <span>{{ guarantor.first_name + ' ' + guarantor.last_name }}</span>
                  <button
                    type="button"
                    @click.stop="removeGuarantor(guarantor)"
                    class="flex-shrink-0 ml-0.5 -mr-1 h-4 w-4 rounded-full inline-flex items-center justify-center focus:outline-none hover:bg-primary-200 focus:bg-primary-500 focus:text-white">
                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8"><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" /></svg>
                  </button>
                </app-badge>
                <button type="button" class="material-icons-outlined rounded-full text-lg px-1.5 focus:outline-none hover:bg-gray-200 focus:bg-gray-300">add</button>
              </app-input>
            </template>

            <template #content>
              <div class="px-3">
                <app-input type="search" class="sm:text-sm" v-model="searchMembers" placeholder="Search Members..."/>
                <div class="mt-1 divide-y divide-gray-200">
                  <div
                    v-for="member in searchMembersResults"
                    class="p-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 cursor-pointer flex items-center space-x-2"
                    @click="addGuarantor(member)">
                    <span>{{ member.first_name }} {{ member.last_name }}</span>
                    <span class="text-gray-400 text-xs">{{ member.email }}</span>
                  </div>
                </div>
                <div class="text-center py-8 text-gray-400" v-if="searchMembersResults.length === 0">No Members.</div>
              </div>
            </template>
          </app-dropdown>
        </div>

      </div>

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-end rounded-b-lg">
        <app-button color="secondary" @click="$emit('close')">Cancel</app-button>
        <app-button :processing="form.processing" class="ml-2" color="primary" type="submit">Submit</app-button>
      </div>

    </form>
  </modal>
</template>
<script>
import Modal from "@/Components/UI/Modal";
import AppCheckbox from "@/Components/UI/Checkbox";
import AppDropdown from "@/Components/UI/Dropdown";
import AppBadge from "@/Components/UI/Badge";

export default {
  components: {
    AppBadge,
    AppDropdown,
    AppCheckbox,
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
      searchMembers: '',
      searchMembersResults: [],
    }
  },

  props: {
    show: Boolean,
    memberId: Number,
    loan: Object,
    dependents: Array
  },

  emits: [
    'close'
  ],

  watch: {
    show(val) {
      this.form = this.freshForm()
      this.searchMembers = ''
      this.searchMembersResults = []
    },

    searchMembers(val) {
      this.fetchMembers()
    },
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        dependent_id: this.loan?.dependent_id || null,
        amount:  this.loan?.amount || null,
        loan_date:  this.loan?.loan_date || null,
        cheque_number:  this.loan?.cheque_number || null,
        application_copy: null,
        guarantors: this.loan?.guarantors || [],
        comment: this.loan?.comment || null,
      })
    },

    submit() {
      let route = this.loan
        ? this.$route('loans.update', this.loan.id)
        : this.$route('members.loans.store', this.memberId)
      this.form
        .transform((data) => ({
          ...data,
          guarantors: data.guarantors.map(e => e.id),
          _method: this.loan ? 'put' : 'post',
        }))
        .post(route, {
          onSuccess: () => this.$emit('close'),
          preserveState: 'errors'
        })
    },

    fetchMembers() {
      if (!this.searchMembers) {
        this.searchMembersResults = []
      } else{
        this.$axios.get(this.$route('members.index'), {params: {limit: 5, search: this.searchMembers}})
          .then(res => this.searchMembersResults = res.data.filter(item => !this.form.guarantors.some(v => v.id === item.id)))
      }
    },

    addGuarantor(member) {
      this.form.guarantors.push(member)
      this.fetchMembers()
    },

    removeGuarantor(guarantor) {
      this.form.guarantors = this.form.guarantors.filter(item => item !== guarantor)
      this.fetchMembers()
    }
  },

  computed: {
    applicationCopyPreview() {
      return this.form.application_copy?.name || this.loan?.application_copy?.split("/").pop() || 'Choose file'
    }
  },
}
</script>
