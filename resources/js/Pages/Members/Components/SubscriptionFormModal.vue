<template>
  <span>

      <app-form-modal :show="show || subscription" @close="$emit('close')" @submitted="submitSubscription">

          <template #title>{{ title }}</template>

          <template #form>

          <div class="grid grid-cols-6 gap-6">

             <div class="col-span-6 sm:col-span-3">
                  <app-label for="type" value="Subscription Type"/>
                  <select id="type" v-model="form.type"
                          class="form-input rounded-md shadow-sm mt-1 block w-full">
                    <option :selected="subscription && subscription.type === 'Membership'">Membership</option>
                    <option :selected="subscription && subscription.type === 'Loan Payment'">Loan Payment</option>
                  </select>
                  <app-input-error :message="form.error('type')" class="mt-1"/>
             </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="amount" value="Amount"/>
                  <app-input id="amount" v-model="form.amount"
                             class="mt-1 block w-full"
                             type="number"/>
                  <app-input-error :message="form.error('amount')" class="mt-1"/>
              </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="start_date" value="Start Date"/>
                  <app-input id="start_date" v-model="form.start_date"
                             class="mt-1 block w-full"
                             type="date"/>
                  <app-input-error :message="form.error('start_date')" class="mt-1"/>
              </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="recurrences" value="Recurrences"/>
                  <app-input id="recurrences" v-model="form.recurrences"
                             class="mt-1 block w-full"
                             type="number"/>
                  <app-input-error :message="form.error('recurrences')" class="mt-1"/>
              </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="frequency" value="Frequency"/>
                  <select id="frequency" v-model="form.frequency"
                          class="form-input rounded-md shadow-sm mt-1 block w-full">
                    <option :selected="subscription && subscription.frequency === 'Monthly'">Monthly</option>
                    <option :selected="subscription && subscription.frequency === 'Bi-Monthly'">Bi-Monthly</option>
                  </select>
                  <app-input-error :message="form.error('frequency')" class="mt-1"/>
             </div>


             <div class="col-span-6 sm:col-span-3">
                  <app-label for="process_day" value="Process Day"/>
                  <app-input
                    id="process_day"
                    v-model="form.process_day"
                    class="mt-1 block w-full"
                    min="1" max="31"
                    type="number"/>
                  <app-input-error :message="form.error('process_day')" class="mt-1"/>
             </div>

          <div v-if="!subscription || changePaymentMethod"
            class="col-span-6 grid grid-cols-6 gap-6 border rounded-md border-gray-300 p-3 bg-gray-100">

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="payment_method_type" value="Payment Type"/>
                  <select id="payment_method_type" v-model="form.payment_method.type"
                          class="form-input rounded-md shadow-sm mt-1 block w-full" :required="!subscription">
                    <option>Credit Card</option>
                    <option>Pre-authorized Debit</option>
                    <option>Cheque</option>
                  </select>
                  <app-input-error :message="form.error('payment_method.type')" class="mt-1"/>
              </div>

              <template v-if="form.payment_method.type === 'Credit Card'">
                <div class="col-span-6 sm:col-span-3">
                    <app-label for="payment_method_name_on_card" value="Name on card"/>
                    <app-input
                      id="payment_method_name_on_card"
                      v-model="form.payment_method.name_on_card"
                      required
                      class="mt-1 block w-full"
                      type="text"/>
                    <app-input-error :message="form.error('payment_method.name_on_card')" class="mt-1"/>
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <app-label for="payment_method_cc_number" value="Credit Card Number"/>
                    <app-input
                      id="payment_method_cc_number"
                      v-model="form.payment_method.cc_number"
                      required
                      class="mt-1 block w-full appearance-none"
                      type="number"/>
                    <app-input-error :message="form.error('payment_method.cc_number')" class="mt-1"/>
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <app-label for="payment_method_cc_expiration" value="Expiry"/>
                    <app-input
                      id="payment_method_cc_expiration"
                      v-model="form.payment_method.cc_expiration"
                      required
                      class="mt-1 block w-full appearance-none"
                      type="month"/>
                    <app-input-error :message="form.error('payment_method.cc_expiration')" class="mt-1"/>
                </div>

              </template>

              <template v-if="form.payment_method.type === 'Pre-authorized Debit'">
                <div class="col-span-6 sm:col-span-3">
                    <app-label for="payment_method_account_number" value="Account Number"/>
                    <app-input
                      id="payment_method_account_number"
                      v-model="form.payment_method.account_number"
                      required
                      class="mt-1 block w-full appearance-none"
                      type="number"/>
                    <app-input-error :message="form.error('payment_method.account_number')" class="mt-1"/>
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <app-label for="payment_method_transit_number" value="Transit Number"/>
                    <app-input
                      id="payment_method_transit_number"
                      v-model="form.payment_method.transit_number"
                      required
                      class="mt-1 block w-full appearance-none"
                      type="number"/>
                    <app-input-error :message="form.error('payment_method.transit_number')" class="mt-1"/>
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <app-label for="payment_method_institution_number" value="Institution Number"/>
                    <app-input
                      id="payment_method_institution_number"
                      v-model="form.payment_method.institution_number"
                      required
                      class="mt-1 block w-full appearance-none"
                      type="number"/>
                    <app-input-error :message="form.error('payment_method.institution_number')" class="mt-1"/>
                </div>

              </template>

            </div>

            <div class="col-span-6" v-else>
              <div class="flex items-center">
                <app-label>Payment Method</app-label>
                <button type="button" class="text-xs focus:outline-none ml-3 hover:underline text-blue-700" @click="changePaymentMethod = true">Change</button>
              </div>
              <app-input disabled class="text-gray-500 bg-gray-50 mt-1 block w-full" :value="paymentMethod"/>
            </div>

          </div>

          </template>

          <template #footer>
              <app-button color="secondary" type="button" @click.native="$emit('close')">
                  Cancel
              </app-button>

              <app-button :processing="form.processing" class="ml-3" color="primary" type="submit">
                  Submit
              </app-button>
          </template>
      </app-form-modal>
  </span>
</template>

<script>
import AppButton from '../../../Shared/Button'
import AppFormModal from '../../../Shared/FormModal'
import AppLabel from '../../../Shared/Label'
import AppInput from '../../../Shared/Input'
import AppInputError from '../../../Shared/InputError'


export default {

  components: {
    AppButton,
    AppFormModal,
    AppLabel,
    AppInput,
    AppInputError,
  },

  props: {
    show: {
      default: false
    },
    subscription: Object,
    membership: Object,
  },

  created() {
    this.resetForm()
  },

  data() {

    return {
      title: this.subscription ? 'Edit Subscription' : 'Create Subscription',
      changePaymentMethod: false,
      form: this.$inertia.form()
    }
  },

  methods: {

    submitSubscription() {

      Object.keys(this.form.payment_method).forEach(key => {
        (this.form.payment_method[key] == null) && delete this.form.payment_method[key]
      });

      if (this.subscription) {
        this.form.put(route('subscriptions.update', this.subscription.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      } else {
        this.form.post(route('memberships.subscriptions.store', this.membership.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }
    },

    resetForm() {
      this.form = this.$inertia.form({
        type: this.subscription?.type || null,
        amount:  this.subscription?.amount || null,
        start_date:  this.subscription?.start_date || null,
        recurrences:  this.subscription?.recurrences || null,
        frequency:  this.subscription?.frequency || null,
        process_day:  this.subscription?.process_day || null,
        payment_method: {
          type: null,
          account_number: null,
          transit_number: null,
          institution_number: null,
          cc_number: null,
          cc_expiration: null,
          name_on_card: null,
        }
      });

      this.$page.props.errorBags['default'] = [];
    }
  },

  watch: {
    show(val) {
      this.resetForm()
    },

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
    },

    subscription(val) {
      this.title = this.subscription ? 'Edit Subscription' : 'Create Subscription';
      this.changePaymentMethod = false
      this.resetForm();
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
