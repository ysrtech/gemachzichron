<template>
  <modal v-if="show" max-width="3xl" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="subscription">Subscription #{{ subscription.id }}</template>
      <template v-else>Add New Subscription</template>
      <div class="text-xs text-gray-500">Member: {{ `${member.first_name} ${member.last_name}` }}</div>
    </div>

    <form @submit.prevent="submit">
<!--      <div class="px-6 pb-4 grid sm:grid-cols-12 gap-x-6 gap-y-4">-->
<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            id="type"-->
<!--            v-model="form.type"-->
<!--            :error="form.errors.type"-->
<!--            label="Subscription Type"-->
<!--            type="select"-->
<!--            @update:model-value="form.clearErrors('type')"-->
<!--            :options="SUBSCRIPTION_TYPES"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            id="gateway"-->
<!--            v-model="form.gateway"-->
<!--            :error="form.errors.gateway"-->
<!--            label="Payment Method"-->
<!--            type="select"-->
<!--            @update:model-value="form.clearErrors('gateway')"-->
<!--            :options="AVAILABLE_GATEWAYS"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            id="start_date"-->
<!--            v-model="form.start_date"-->
<!--            :error="form.errors.start_date"-->
<!--            label="Start Date"-->
<!--            type="date"-->
<!--            @update:model-value="form.clearErrors('start_date')"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            id="frequency"-->
<!--            v-model="form.frequency"-->
<!--            :error="form.errors.frequency"-->
<!--            label="Frequency"-->
<!--            type="select"-->
<!--            :options="SUBSCRIPTION_FREQUENCIES"-->
<!--            @update:model-value="form.clearErrors('frequency')"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            v-show="form.frequency && form.frequency !== SUBSCRIPTION_FREQUENCIES.Once"-->
<!--            id="installments"-->
<!--            v-model="form.installments"-->
<!--            :error="form.errors.installments"-->
<!--            label="Installments"-->
<!--            placeholder="Unlimited"-->
<!--            class="placeholder-gray-700"-->
<!--            type="number"-->
<!--            min="1"-->
<!--            @update:model-value="form.clearErrors('installments')"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4 sm:col-start-1">-->
<!--          <app-input-->
<!--            id="amount"-->
<!--            v-model="form.amount"-->
<!--            :error="form.errors.amount"-->
<!--            label="Membership Amount"-->
<!--            type="number"-->
<!--            step="0.01"-->
<!--            min="1"-->
<!--            @update:model-value="form.clearErrors('amount')"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            id="membership_fee"-->
<!--            v-model="form.membership_fee"-->
<!--            :error="form.errors.membership_fee"-->
<!--            label="Membership Fee"-->
<!--            type="number"-->
<!--            step="0.01"-->
<!--            @update:model-value="form.clearErrors('membership_fee')"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            id="processing_fee"-->
<!--            v-model="form.processing_fee"-->
<!--            :error="form.errors.processing_fee"-->
<!--            label="Credit Card Processing Fee"-->
<!--            type="number"-->
<!--            step="0.01"-->
<!--            @update:model-value="form.clearErrors('processing_fee')"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            v-show="form.frequency === SUBSCRIPTION_FREQUENCIES.Once"-->
<!--            id="decline_fee"-->
<!--            v-model="form.decline_fee"-->
<!--            :error="form.errors.decline_fee"-->
<!--            label="Decline fee"-->
<!--            type="number"-->
<!--            step="0.01"-->
<!--            @update:model-value="form.clearErrors('decline_fee')"-->
<!--          />-->
<!--        </div>-->

<!--        <div class="sm:col-span-4">-->
<!--          <app-input-->
<!--            label="Total Amount"-->
<!--            type="div"-->
<!--            class="bg-gray-200 text-gray-900"-->
<!--          >-->
<!--            <money :amount="totalAmount"/>-->
<!--          </app-input>-->
<!--        </div>-->

<!--        <div class="sm:col-span-8 sm:col-start-1">-->
<!--          <app-input-->
<!--            id="comment"-->
<!--            class="h-12"-->
<!--            v-model="form.comment"-->
<!--            :error="form.errors.comment"-->
<!--            label="Comments"-->
<!--            type="textarea"-->
<!--            @update:model-value="form.clearErrors('comment')"-->
<!--          />-->
<!--        </div>-->
<!--      </div>-->

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-end rounded-b-lg space-x-2">
        <app-button color="secondary" @click="$emit('close')">
          Cancel
        </app-button>
        <app-button :processing="form.processing" color="primary" type="submit">
          Submit
        </app-button>
      </div>

    </form>
  </modal>
</template>

<script>
import Modal from "@/Components/UI/Modal";
import {DEFAULT_SUBSCRIPTION_FEES, SUBSCRIPTION_FREQUENCIES, SUBSCRIPTION_TYPES} from "@/config/subscriptions";
import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import Money from "@/Components/UI/Money";

export default {
  components: {
    Money,
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
      SUBSCRIPTION_TYPES,
      SUBSCRIPTION_FREQUENCIES,
      AVAILABLE_GATEWAYS,
    }
  },

  props: {
    show: Boolean,
    subscription: Object,
    member: {
      type: Object,
      required: true,
    },
  },

  emits: [
    'close'
  ],

  watch: {
    show() {
      this.form = this.freshForm()
    },

    'form.gateway'(val) {
      if (this.form.processing_fee === undefined) {
        this.form.processing_fee = DEFAULT_SUBSCRIPTION_FEES.processingFee(val, Number(this.form.amount || 0) + Number(this.form.membership_fee || 0))
      }
    },

    'form.amount'(val) {
      if (this.form.membership_fee === undefined) {
        this.form.membership_fee = DEFAULT_SUBSCRIPTION_FEES.membershipFee(val)
      }

      this.form.processing_fee = DEFAULT_SUBSCRIPTION_FEES.processingFee(this.form.gateway, Number(val) + Number(this.form.membership_fee))
    },

    'form.frequency'(newVal, oldVal) {
      if (newVal === SUBSCRIPTION_FREQUENCIES.Once) {
        this.form.installments = '1'
      } else if (oldVal === SUBSCRIPTION_FREQUENCIES.Once) {
        this.form.installments = null
        this.form.decline_fee = null
      }
    }
  },

  computed: {
    totalAmount() {
      return Number(this.form.amount || 0)
        + Number(this.form.membership_fee || 0)
        + Number(this.form.processing_fee || 0)
        + Number(this.form.decline_fee || 0)
    }
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        type: this.subscription?.type,
        gateway: this.subscription?.gateway,
        start_date: this.subscription?.start_date,
        installments: this.subscription?.installments,
        frequency: this.subscription?.frequency,
        amount: this.subscription?.amount,
        membership_fee: this.subscription?.membership_fee,
        processing_fee: this.subscription?.processing_fee,
        decline_fee: this.subscription?.decline_fee,
        comment: this.subscription?.comment,
      })
    },

    submit() {
      let options = { onSuccess: () => this.$emit('close') }
      if (this.subscription) {
        this.form.put(
          this.$route('subscriptions.update', this.subscription.id),
          options
        )
      } else {
        this.form.post(
          this.$route('members.subscriptions.store', this.member.id),
          options
        )
      }
    },

  },
}
</script>
