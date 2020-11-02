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
                    <option :value="1" :selected="subscription && subscription.type === 'Membership'">Membership</option>
                    <option :value="2" :selected="subscription && subscription.type === 'Loan Payment'">Loan Payment</option>
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
                    <option :value="1" :selected="subscription && subscription.frequency === 'Monthly'">Monthly</option>
                    <option :value="2" :selected="subscription && subscription.frequency === 'Bi-Monthly'">Bi-Monthly</option>
                  </select>
                  <app-input-error :message="form.error('frequency')" class="mt-1"/>
             </div>


             <div class="col-span-6 sm:col-span-3">
                  <app-label for="process_day" value="Process Day"/>
                  <select id="process_day" v-model="form.process_day"
                          class="form-input rounded-md shadow-sm mt-1 block w-full">
                    <option v-for="n in 31" :value="n" :selected="subscription && subscription.process_day == n">{{ n }}</option>
                  </select>
                  <app-input-error :message="form.error('process_day')" class="mt-1"/>
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


  data() {

    return {
      title: this.subscription ? 'Edit Subscription' : 'Create Subscription',
      form: this.$inertia.form()
    }
  },

  methods: {

    submitSubscription() {
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
        type: this.subscription?.type === 'Membership' ? 1 : (this.subscription?.type === 'Loan Payment' ? 2 : null),
        amount:  this.subscription?.amount || null,
        start_date:  this.subscription?.start_date || null,
        recurrences:  this.subscription?.recurrences || null,
        frequency:  this.subscription?.frequency === 'Monthly' ? 1 : (this.subscription?.frequency === 'Bi-Monthly' ? 2 : null),
        process_day:  this.subscription?.process_day || null,
      })
    }
  },

  watch: {
    show(val) {
      this.resetForm()
    },

    subscription(val) {
      this.title = this.subscription ? 'Edit Subscription' : 'Create Subscription';
      this.resetForm();
    }

  },
}
</script>
