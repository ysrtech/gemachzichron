<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')" class="overflow-visible">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="withdrawal">Edit Withdrawal</template>
      <template v-else>Add New Withdrawal</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 pb-4 grid sm:grid-cols-2 gap-x-6 gap-y-4">
        
        <app-input
          id="amount"
          v-model="form.amount"
          :error="form.errors.amount"
          label="Amount"
          type="number"
          step="0.01"
          @update:model-value="form.clearErrors('amount')"
        />

        <app-input
          id="withdrawal_date"
          v-model="form.withdrawal_date"
          :error="form.errors.withdrawal_date"
          label="Withdrawal Date"
          type="date"
          @update:model-value="form.clearErrors('withdrawal_date')"
        />

        <app-select
          native
          id="method"
          v-model="form.method"
          :error="form.errors.method"
          label="Withdrawal Method"
          @update:model-value="form.clearErrors('method')">
          <template #options>
            <option value="">Select Method</option>
            <option value="Cheque">Cheque</option>
            <option value="Etransfer">Etransfer</option>
            <option value="Loan Payoff">Loan Payoff</option>
          </template>
        </app-select>

        <div class="col-span-2">
          <app-textarea
            id="comment"
            v-model="form.comment"
            :error="form.errors.comment"
            label="Comments"
            @update:model-value="form.clearErrors('comment')"
          />
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
import AppInput from "@/Components/FormControls/Input"
import AppSelect from "@/Components/FormControls/Select";
import AppTextarea from "@/Components/FormControls/Textarea";

export default {
  components: {
    AppTextarea,
    AppSelect,
    AppInput,
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
    }
  },

  props: {
    show: Boolean,
    memberId: Number,
    withdrawal: Object,
  },

  emits: [
    'close'
  ],

  watch: {
    show(val) {
      this.form = this.freshForm()
    },
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        amount:  this.withdrawal?.amount || null,
        withdrawal_date:  this.withdrawal?.withdrawal_date || null,
        method:  this.withdrawal?.method || '',
        comment: this.withdrawal?.comment || null,
      })
    },

    submit() {
      let route = this.withdrawal
        ? this.$route('withdrawals.update', this.withdrawal.id)
        : this.$route('members.withdrawals.store', this.memberId)
      this.form
        .transform((data) => ({
          ...data,
          _method: this.withdrawal ? 'put' : 'post',
        }))
        .post(route, {
          onSuccess: () => this.$emit('close'),
          preserveState: 'errors'
        })
    },
  },
}
</script>
