<template>
  <modal v-if="show" max-width="2xl" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium flex items-start justify-between">
      <div>
        Make Transaction
        <div class="text-xs text-gray-500">Manual Subscription #{{ subscription.id }}</div>
        <app-errors :error="form.errors.gateway"/>
      </div>
      <div class="text-sm text-gray-700 flex space-x-2">
        <span>Total</span> <money :amount="subscription.transaction_total"/>
      </div>
    </div>

    <div class=""></div>
    <form @submit.prevent="submit">
      <div class="px-6 pb-4 grid gap-4 sm:grid-cols-2">

        <app-input
          id="process_date"
          v-model="form.process_date"
          :error="form.errors.process_date"
          label="Process Date"
          type="date"
          @update:model-value="form.clearErrors('process_date')"
        />

        <app-textarea
          id="comment"
          class="h-12"
          v-model="form.comment"
          :error="form.errors.comment"
          label="Comments"
          @update:model-value="form.clearErrors('comment')"
        />
      </div>

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-end rounded-b-lg">
        <div class="flex justify-end space-x-3">
          <app-button color="secondary" @click="$emit('close')">
            Cancel
          </app-button>
          <app-button :processing="form.processing" color="primary" type="submit">
            Submit
          </app-button>
        </div>
      </div>

    </form>
  </modal>
</template>

<script>
import Modal from "@/Components/Modal";
import AppInput from "@/Components/FormControls/Input"
import AppTextarea from "@/Components/FormControls/Textarea";
import AppErrors from "@/Components/FormControls/Errors";
import {TRANSACTION_TYPES} from "@/config/transactions";
import Money from "@/Components/Money";

export default {
  components: {
    Money,
    AppErrors,
    AppTextarea,
    AppInput,
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
      TRANSACTION_TYPES,
    }
  },

  props: {
    show: Boolean,
    subscription: Object,
  },

  emits: [
    'close'
  ],

  watch: {
    show() {
      this.form = this.freshForm()
    },
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        process_date: '',
        comment: '',
      })
    },

    submit() {
      this.form.post(this.$route('subscriptions.transactions.store', this.subscription.id), {
        onSuccess: () => this.$emit('close')
      })
    },
  },
}
</script>
