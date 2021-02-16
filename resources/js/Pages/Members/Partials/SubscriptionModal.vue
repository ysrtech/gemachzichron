<template>
  <modal :show="show" max-width="lg" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="subscription">Edit subscription</template>
      <template v-else>Create subscription</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 pb-4 grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <app-input
          id="type"
          v-model="form.type"
          :error="form.errors.type"
          label="Subscription Type"
          type="select"
          @input="form.clearErrors('type')">
          <template #options>
            <option>Membership</option>
            <option>Loan Payment</option>
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
          id="start_date"
          v-model="form.start_date"
          :error="form.errors.start_date"
          label="Start Date"
          type="date"
          @input="form.clearErrors('start_date')"
        />

        <app-input
          id="recurrences"
          v-model="form.recurrences"
          :error="form.errors.recurrences"
          label="Recurrences"
          type="number"
          @input="form.clearErrors('recurrences')"
        />

        <app-input
          id="frequency"
          v-model="form.frequency"
          :error="form.errors.frequency"
          label="Frequency"
          type="select"
          @input="form.clearErrors('frequency')">
          <template #options>
            <option>Monthly</option>
            <option>Bi-Monthly</option>
          </template>
        </app-input>

        <app-input
          id="process_day"
          v-model="form.process_day"
          :error="form.errors.process_day"
          label="Process Day"
          type="number"
          min="1" max="31"
          @input="form.clearErrors('process_day')"
        />

        <div v-if="!subscription || changePaymentMethod" class="col-span-2 grid sm:grid-cols-2 gap-6 border rounded-md border-gray-300 p-3 bg-gray-100">

          <app-input
            id="payment_method_type"
            v-model="form.payment_method.type"
            :error="form.errors['payment_method.type']"
            label="Payment Type"
            type="select"
            @input="form.clearErrors('payment_method.type')">
            <template #options>
              <option>Credit Card</option>
              <option>Pre-authorized Debit</option>
              <option>Cheque</option>
            </template>
          </app-input>

          <template v-if="form.payment_method.type === 'Credit Card'">
            <app-input
              id="payment_method_name_on_card"
              v-model="form.payment_method.name_on_card"
              :error="form.errors['payment_method.name_on_card']"
              label="Name on card"
              type="text"
              @input="form.clearErrors('payment_method.name_on_card')"
            />

            <app-input
              id="payment_method_cc_number"
              v-model="form.payment_method.cc_number"
              :error="form.errors['payment_method.cc_number']"
              label="Credit Card Number"
              type="number"
              @input="form.clearErrors('payment_method.cc_number')"
            />

            <app-input
              id="payment_method_cc_expiration"
              v-model="form.payment_method.cc_expiration"
              :error="form.errors['payment_method.cc_expiration']"
              label="Expiry"
              type="month"
              @input="form.clearErrors('payment_method.cc_expiration')"
            />
          </template>

          <template v-if="form.payment_method.type === 'Pre-authorized Debit'">
            <app-input
              id="payment_method_account_number"
              v-model="form.payment_method.account_number"
              :error="form.errors['payment_method.account_number']"
              label="Account Number"
              type="number"
              @input="form.clearErrors('payment_method.account_number')"
            />

            <app-input
              id="payment_method_transit_number"
              v-model="form.payment_method.transit_number"
              :error="form.errors['payment_method.transit_number']"
              label="Transit Number"
              type="number"
              @input="form.clearErrors('payment_method.transit_number')"
            />

            <app-input
              id="payment_method_institution_number"
              v-model="form.payment_method.institution_number"
              :error="form.errors['payment_method.institution_number']"
              label="Institution Number"
              type="number"
              @input="form.clearErrors('payment_method.institution_number')"
            />
          </template>

        </div>

        <div class="col-span-2" v-else>
          <app-input
            id="payment_method"
            :modelValue="paymentMethod"
            label="Payment Method"
            type="text"
            disabled
            class="text-gray-500 bg-gray-50"
          >
            <template #top-right>
              <button type="button" class="text-xs focus:outline-none ml-3 hover:underline text-gray-700" @click="changePaymentMethod = true">Change</button>
            </template>
          </app-input>
        </div>

      </div>

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-end">
        <app-button color="secondary" @click="$emit('close')">Cancel</app-button>
        <app-button :processing="form.processing" class="ml-2" color="primary" type="submit">Submit</app-button>
      </div>

    </form>
  </modal>
</template>
<script>
import Modal from "@/Components/UI/Modals/Modal";

export default {
  components: {
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
      changePaymentMethod: false,
    }
  },

  props: {
    show: Boolean,
    membershipId: Number,
    subscription: {
      type: Object,
      default: null
    },
  },

  emits: [
    'close'
  ],

  watch: {

    'form.payment_method.type'(val) {
      this.form.payment_method = {
        type: val,
        account_number: null,
        transit_number: null,
        institution_number: null,
        cc_number: null,
        cc_expiration: null,
        name_on_card: null,
      }
      this.form.clearErrors('payment_method')
    },

    show(val) {
      this.changePaymentMethod = false
      this.form = this.freshForm()
    }
  },

  methods: {
    submit() {
      if (this.subscription) {
        this.form.put(this.$route('subscriptions.update', this.subscription.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('memberships.subscriptions.store', this.membershipId), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
    freshForm() {
      return this.$inertia.form({
        type: this.subscription?.type || null,
        amount:  this.subscription?.amount || null,
        start_date:  this.subscription?.start_date || null,
        recurrences:  this.subscription?.recurrences || null,
        frequency:  this.subscription?.frequency || null,
        process_day:  this.subscription?.process_day || null,
        payment_method: {}
      })
    }
  },

  computed: {
    paymentMethod() {
      let type = this.subscription?.payment_method?.type;

      if (type === 'Credit Card') {
        type += `   ***${this.subscription.payment_method.last_four_digits}   exp ${this.subscription.payment_method.cc_expiration}`
      }

      return type
    }
  }
}
</script>
