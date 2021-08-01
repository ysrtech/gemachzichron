<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')" class="overflow-visible">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="loan">Edit Loan</template>
      <template v-else>Add New Loan</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 pb-4 grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <app-select
          native
          id="type"
          v-model="form.dependent_id"
          :error="form.errors.dependent_id"
          label="Dependent"
          @update:model-value="form.clearErrors('dependent_id')">
          <template #options>
            <option :value="null">--</option>
            <option
              v-for="dependent in dependents"
              :key="dependent.id"
              :value="dependent.id">
              {{ dependent.name }}
            </option>
          </template>
        </app-select>

        <app-input
          id="amount"
          v-model="form.amount"
          :error="form.errors.amount"
          label="Amount"
          type="number"
          @update:model-value="form.clearErrors('amount')"
        />

        <app-input
          id="loan_date"
          v-model="form.loan_date"
          :error="form.errors.loan_date"
          label="Loan Date"
          type="date"
          @update:model-value="form.clearErrors('loan_date')"
        />

        <app-input
          id="cheque_number"
          v-model="form.cheque_number"
          :error="form.errors.cheque_number"
          label="Cheque Number"
          type="text"
          @update:model-value="form.clearErrors('cheque_number')"
        />

        <div class="col-span-2">
          <app-file-input
            id="application_copy"
            v-model="form.application_copy"
            :error="form.errors.application_copy"
            label="Application Copy"
            :filename="applicationCopyPreview"
            @update:model-value="form.clearErrors('application_copy')"
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
          </app-file-input>
        </div>

        <div class="col-span-2">
          <app-textarea
            id="comment"
            v-model="form.comment"
            :error="form.errors.comment"
            label="Comments"
            @update:model-value="form.clearErrors('comment')"
          />
        </div>

        <div class="col-span-2">
          <app-select
            label="Guarantors"
            :multiple="true"
            :searchable="true"
            :taggable="true"
            :hide-selected="true"
            :clear-on-close="true"
            v-model="form.guarantors"
            :options="guarantorsOptions"
            :loading="membersLoading"
            :label-by="e => `${e.first_name} ${e.last_name}`"
            :error="form.errors.guarantors"
            @search:input="fetchMembers"
          >
            <template #tag="{ option, remove }">
              <app-badge class="inline-flex items-center">
                <span>{{ option.first_name + ' ' + option.last_name }}</span>
                <button
                  type="button"
                  @click.stop="remove"
                  class="flex-shrink-0 ml-0.5 -mr-1 h-4 w-4 rounded-full inline-flex items-center justify-center focus:outline-none hover:bg-primary-200 focus:bg-primary-500 focus:text-white">
                  <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8"><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" /></svg>
                </button>
              </app-badge>
            </template>
          </app-select>
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
import Modal from "@/Components/Modal";
import AppCheckbox from "@/Components/FormControls/Checkbox";
import AppInput from "@/Components/FormControls/Input"
import AppSelect from "@/Components/FormControls/Select";
import AppFileInput from "@/Components/FormControls/FileInput";
import AppTextarea from "@/Components/FormControls/Textarea";
import AppMockInput from "@/Components/FormControls/MockInput";

export default {
  components: {
    AppMockInput,
    AppTextarea,
    AppFileInput,
    AppSelect,
    AppInput,
    AppCheckbox,
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
      membersLoading: false,
      membersResults: []
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
      this.membersResults = []
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

    async fetchMembers(query) {
      if (!query) {
        this.membersResults = []
        return
      }
      this.membersLoading = true
      const res = await this.$axios.get(this.$route('members.index'), {
        params: {
          search: query,
          limit: 10,
        },
      })
      this.membersResults = res.data
      this.membersLoading = false
    },
  },

  computed: {
    applicationCopyPreview() {
      return this.form.application_copy?.name || this.loan?.application_copy?.split("/").pop() || 'Choose file'
    },
    guarantorsOptions() {
      return this.form.guarantors.concat(this.membersResults.filter(member => !this.form.guarantors.find(guarantor => guarantor.id == member.id)))
    }
  },
}
</script>
