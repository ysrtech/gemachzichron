<template>
  <modal v-if="show" max-width="md" @close="$emit('close')" class="overflow-visible">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="paymentMethod">Edit Payment Method</template>
      <template v-else>Add New Payment Method</template>
    </div>

    <form @submit.prevent="submit">

      <div class="px-6 py-4 space-y-4">
        <app-input
          id="gateway"
          v-model="form.gateway"
          :error="form.errors.gateway"
          label="Gateway"
          type="select"
          :disabled="!!paymentMethod"
          @input="form.clearErrors('gateway')"
        >
          <template #options>
<!--            <option v-for="gateway in AVAILABLE_GATEWAYS">{{ gateway }}</option>-->
            <option>{{ AVAILABLE_GATEWAYS.Rotessa }}</option>
          </template>
        </app-input>

        <div v-if="form.gateway === AVAILABLE_GATEWAYS.Rotessa" class="grid sm:grid-cols-2 gap-6">
          <app-input
            id="bank_name"
            v-model="form.bank_name"
            :error="form.errors.bank_name"
            label="Bank Name"
            type="text"
            @input="form.clearErrors('bank_name')"
          />
          <app-input
            id="transit_number"
            v-model="form.transit_number"
            :error="form.errors.transit_number"
            label="Transit Number"
            type="text"
            @input="form.clearErrors('transit_number')"
          />
          <app-input
            id="institution_number"
            v-model="form.institution_number"
            :error="form.errors.institution_number"
            label="Institution Number"
            type="text"
            @input="form.clearErrors('institution_number')"
          />
          <app-input
            id="account_number"
            v-model="form.account_number"
            :error="form.errors.account_number"
            label="Account Number"
            type="text"
            @input="form.clearErrors('account_number')"
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
import Modal from "@/Components/UI/Modal";
import {AVAILABLE_GATEWAYS} from "@/config/gateways";

export default {
  components: {
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
    membership: Object,
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

    show(val) {
      this.form = this.freshForm()
    },
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        gateway: this.paymentMethod?.gateway,
        bank_name: this.paymentMethod?.data.bank_name,
        transit_number: '',
        institution_number: '',
        account_number: '',
      })
    },

    submit() {
      if (this.paymentMethod) {
        this.form.put(this.$route('payment-methods.update', this.paymentMethod.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('memberships.payment-methods.store', this.membership?.id), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },
}
</script>
