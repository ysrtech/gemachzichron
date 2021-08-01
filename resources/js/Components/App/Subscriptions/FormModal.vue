<template>
  <modal v-if="show" max-width="2xl" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="subscription">Subscription #{{ subscription.id }}</template>
      <template v-else>Add New Subscription</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 pb-4 grid sm:grid-cols-12 gap-x-6 gap-y-4">

        <div class="sm:col-span-6">
          <app-select
            id="member"
            v-model="member"
            label="Member"
            required
            :searchable="true"
            :clear-on-close="true"
            :clear-on-select="true"
            :close-on-select="true"
            :hide-selected="true"
            :disabled="!!memberProp"
            :options="membersOptions"
            :loading="membersLoading"
            :label-by="m => `${m.first_name} ${m.last_name}`"
            :error="memberFieldError"
            @update:model-value="memberFieldError = null"
            @search:input="fetchMembers"
          />
        </div>

        <div class="sm:col-span-6">
          <app-select
            native
            id="type"
            v-model="form.type"
            :error="form.errors.type"
            label="Subscription Type"
            @update:model-value="form.clearErrors('type')"
            :options="SUBSCRIPTION_TYPES"
          />
        </div>

        <div class="sm:col-span-6">
          <app-select
            native
            id="gateway"
            v-model="form.gateway"
            :error="form.errors.gateway"
            label="Payment Method"
            @update:model-value="form.clearErrors('gateway')">
            <template #options>
              <option></option>
              <option v-for="paymentMethod in member?.payment_methods" :key="paymentMethod.id">
                {{ paymentMethod.gateway }}
              </option>
<!--              <option>{{ AVAILABLE_GATEWAYS.Manual }}</option>-->
            </template>
          </app-select>
        </div>

        <div class="sm:col-span-6">
          <app-input
            id="start_date"
            v-model="form.start_date"
            :error="form.errors.start_date"
            label="Start Date"
            type="date"
            @update:model-value="form.clearErrors('start_date')"
          />
        </div>

        <div class="sm:col-span-6">
          <app-select
            native
            id="frequency"
            v-model="form.frequency"
            :error="form.errors.frequency"
            label="Frequency"
            :options="SUBSCRIPTION_FREQUENCIES"
            @update:model-value="form.clearErrors('frequency')"
          />
        </div>

        <div class="sm:col-span-6">
          <app-number-input
            :readonly="form.frequency === SUBSCRIPTION_FREQUENCIES.Once"
            id="installments"
            v-model="form.installments"
            :error="form.errors.installments"
            label="Installments"
            placeholder="Unlimited"
            class="placeholder-gray-700"
            min="1"
            @update:model-value="form.clearErrors('installments')"
          />
        </div>

        <div class="sm:col-span-6">
          <app-input
            id="amount"
            v-model="form.amount"
            :error="form.errors.amount"
            label="Base Amount"
            type="number"
            step="0.01"
            min="1"
            @update:model-value="form.clearErrors('amount')"
          />
        </div>

        <div class="sm:col-span-6">
          <app-input
            id="membership_fee"
            v-model="form.membership_fee"
            :error="form.errors.membership_fee"
            label="Membership Fee"
            type="number"
            min="0"
            step="0.01"
            @update:model-value="form.clearErrors('membership_fee')"
          />
        </div>

        <div class="sm:col-span-6">
          <app-input
            id="processing_fee"
            v-model="form.processing_fee"
            :error="form.errors.processing_fee"
            label="Credit Card Processing Fee"
            type="number"
            min="0"
            step="0.01"
            @update:model-value="form.clearErrors('processing_fee')"
          />
        </div>

        <div class="sm:col-span-6">
          <app-input
            :readonly="form.frequency !== SUBSCRIPTION_FREQUENCIES.Once"
            :placeholder="form.frequency !== SUBSCRIPTION_FREQUENCIES.Once ? 'Enabled with single installment only' : ''"
            id="decline_fee"
            v-model="form.decline_fee"
            :error="form.errors.decline_fee"
            label="Decline fee"
            type="number"
            min="0"
            step="0.01"
            @update:model-value="form.clearErrors('decline_fee')"
          />
        </div>

        <div class="sm:col-span-12">
          <app-textarea
            id="comment"
            class="h-12"
            v-model="form.comment"
            :error="form.errors.comment"
            label="Comments"
            @update:model-value="form.clearErrors('comment')"
          />
        </div>
      </div>

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-between rounded-b-lg">
        <div class="flex flex-col font-medium">
          <div class="text-xs">Full transaction amount</div>
          <money :amount="totalAmount"/>
        </div>
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
import {DEFAULT_SUBSCRIPTION_FEES, SUBSCRIPTION_FREQUENCIES, SUBSCRIPTION_TYPES} from "@/config/subscriptions";
import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import Money from "@/Components/Money";
import AppInput from "@/Components/FormControls/Input"
import AppSelect from "@/Components/FormControls/Select";
import AppTextarea from "@/Components/FormControls/Textarea";
import AppMockInput from "@/Components/FormControls/MockInput";
import AppNumberInput from "@/Components/FormControls/NumberInput";

export default {
  components: {
    AppNumberInput,
    AppMockInput,
    AppTextarea,
    AppSelect,
    AppInput,
    Money,
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
      member: this.memberProp,
      SUBSCRIPTION_TYPES,
      SUBSCRIPTION_FREQUENCIES,
      AVAILABLE_GATEWAYS,
      membersLoading: false,
      membersResults: [],
      memberFieldError: null
    }
  },

  props: {
    show: Boolean,
    subscription: Object,
    memberProp: Object,
    failedTransaction: Object,
  },

  emits: [
    'close'
  ],

  watch: {
    show() {
      this.member = this.memberProp
      this.memberFieldError = null
      this.form = this.freshForm()
    },

    member() {
      this.form.gateway = null
    },

    // 'form.gateway'(val) {
    //   if (this.form.processing_fee === undefined) {
    //     this.form.processing_fee = DEFAULT_SUBSCRIPTION_FEES.processingFee(val, Number(this.form.amount || 0) + Number(this.form.membership_fee || 0))
    //   }
    // },
    //
    // 'form.amount'(val) {
    //   if (this.form.membership_fee === undefined) {
    //     this.form.membership_fee = DEFAULT_SUBSCRIPTION_FEES.membershipFee(val)
    //   }
    //
    //   this.form.processing_fee = DEFAULT_SUBSCRIPTION_FEES.processingFee(this.form.gateway, Number(val) + Number(this.form.membership_fee))
    // },
    //
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
    membersOptions() {
      if (!this.member) return this.membersResults
      return this.membersResults.concat([this.member])
    },

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
        type: this.subscription?.type || null,
        gateway: this.subscription?.gateway || null,
        start_date: this.subscription?.start_date || null,
        installments: this.subscription?.installments || null,
        frequency: this.subscription?.frequency || null,
        amount: this.subscription?.amount || null,
        membership_fee: this.subscription?.membership_fee || null,
        processing_fee: this.subscription?.processing_fee || null,
        decline_fee: this.subscription?.decline_fee || null,
        comment: this.subscription?.comment || null,
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
          with: 'paymentMethods:id,member_id,gateway'
        },
      })
      this.membersResults = res.data
      this.membersLoading = false
    },

    submit() {
      if (!this.member) {
        this.memberFieldError = 'This field is required'
        return
      }

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
