<template>
  <modal v-if="show" max-width="md" @close="$emit('close')" class="overflow-visible">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="paymentMethod">Edit Payment Method</template>
      <template v-else>Add New Payment Method</template>
    </div>

    <form @submit.prevent="submit">

      <div class="px-6 py-4 space-y-4">
        <app-select
          native
          id="gateway"
          v-model="form.gateway"
          :error="form.errors.gateway"
          label="Gateway"
          :disabled="!!paymentMethod"
          @update:model-value="form.clearErrors('gateway')"
        >
          <template #options>
            <option>{{ AVAILABLE_GATEWAYS.Rotessa }}</option>
            <option>{{ AVAILABLE_GATEWAYS.Cardknox }}</option>
          </template>
        </app-select>

        <div v-if="form.gateway === AVAILABLE_GATEWAYS.Rotessa" class="grid sm:grid-cols-2 gap-6">
          <app-input
            id="bank_name"
            v-model="form.bank_name"
            :error="form.errors.bank_name"
            label="Bank Name"
            type="text"
            @update:model-value="form.clearErrors('bank_name')"
          />
          <app-input
            id="transit_number"
            v-model="form.transit_number"
            :error="form.errors.transit_number"
            label="Transit Number"
            type="text"
            @update:model-value="form.clearErrors('transit_number')"
          />
          <app-input
            id="institution_number"
            v-model="form.institution_number"
            :error="form.errors.institution_number"
            label="Institution Number"
            type="text"
            @update:model-value="form.clearErrors('institution_number')"
          />
          <app-input
            id="account_number"
            v-model="form.account_number"
            :error="form.errors.account_number"
            label="Account Number"
            type="text"
            @update:model-value="form.clearErrors('account_number')"
          />
        </div>

        <div v-if="form.gateway === AVAILABLE_GATEWAYS.Cardknox" class="grid sm:grid-cols-2 gap-6">
          <app-input
            id="card_number"
            v-model="form.card_number"
            :error="form.errors.card_number"
            label="Card Number"
            type="text"
            @update:model-value="form.clearErrors('card_number')"
          />
          <app-input
            id="card_expiry"
            v-model="form.card_expiry"
            :error="form.errors.card_expiry"
            label="Exp. Date"
            type="text"
            minlength="4"
            maxlength="4"
            pattern="\d{2}\d{2}"
            placeholder="MMYY"
            @update:model-value="form.clearErrors('card_expiry')"
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
import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import AppInput from "@/Components/FormControls/Input"
import AppSelect from "@/Components/FormControls/Select";

export default {
  components: {
    AppSelect,
    AppInput,
    Modal
  },

  data() {
    return {
      form: this.freshForm(),
      AVAILABLE_GATEWAYS,
    }
  },

  props: {
    show: Boolean,
    member: Object,
    paymentMethod: Object,
  },

  emits: [
    'close'
  ],

  watch: {
    'form.gateway'(val) {
      this.form = this.freshForm()
      this.form.gateway = val
    },

    show() {
      this.form = this.freshForm()
    },
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        gateway: this.paymentMethod?.gateway,
        bank_name: this.paymentMethod?.gateway_data?.bank_name,
        transit_number: '',
        institution_number: '',
        account_number: '',
        card_number: '',
        card_expiry: '',
      })
    },

    submit() {
      if (this.paymentMethod) {
        this.form.put(this.$route('payment-methods.update', this.paymentMethod.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('members.payment-methods.store', this.member?.id), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },
}
</script>
