<template>
  <span>

      <app-form-modal :show="show || loan" @close="$emit('close')" @submitted="submitLoan">

          <template #title>{{ loan ? 'Edit Loan' : 'Create Loan' }}</template>

          <template #form>

          <div class="grid grid-cols-6 gap-6">

             <div class="col-span-6 sm:col-span-3">
                  <app-label for="dependent_id" value="Dependent"/>
                  <select id="dependent_id" v-model="form.dependent_id" class="form-input rounded-md shadow-sm mt-1 block w-full">
                    <option
                      v-for="dependent in dependents"
                      :key="dependent.id"
                      :value="dependent.id"
                      :selected="loan && loan.dependent_id === dependent.id">
                      {{ dependent.first_name + ' ' + dependent.last_name}}
                    </option>
                  </select>
                  <app-input-error :message="form.error('dependent_id')" class="mt-1"/>
             </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="amount" value="Amount"/>
                  <app-input id="amount" v-model="form.amount"
                             class="mt-1 block w-full"
                             type="number"/>
                  <app-input-error :message="form.error('amount')" class="mt-1"/>
              </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="loan_date" value="Loan Date"/>
                  <app-input id="loan_date" v-model="form.loan_date"
                             class="mt-1 block w-full"
                             type="date"/>
                  <app-input-error :message="form.error('loan_date')" class="mt-1"/>
              </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="cheque_number" value="Cheque Number"/>
                  <app-input id="cheque_number" v-model="form.cheque_number"
                             class="mt-1 block w-full"
                             type="text"/>
                  <app-input-error :message="form.error('cheque_number')" class="mt-1"/>
              </div>

              <div class="col-span-6 sm:col-span-3">
                  <app-label for="application_copy" value="Application Copy"/>
                  <app-label for="application_copy">
                    <div class="h-10 form-input rounded-md shadow-sm mt-1 block w-full text-gray-500 font-normal">
                      {{ applicationCopyPreview }}
                    </div>
                  </app-label>
                <app-input-error :message="form.error('application_copy')" class="mt-1"/>
                  <input
                    id="application_copy"
                    @change="form.application_copy = $event.target.files[0]"
                    class="invisible h-0"
                    type="file"/>
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
    loan: Object,
    membershipId: Number,
    dependents: Array
  },


  data() {

    return {
      form: this.$inertia.form()
    }
  },

  methods: {

    submitLoan() {
      if (this.loan) {
        this.form._method = 'PUT';
        this.form.post(route('loans.update', this.loan.id), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      } else {
        this.form.post(route('memberships.loans.store', this.membershipId), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }
    },

    resetForm() {
      this.form = this.$inertia.form({
        dependent_id: this.loan?.dependent_id || null,
        amount:  this.loan?.amount || null,
        loan_date:  this.loan?.loan_date || null,
        cheque_number:  this.loan?.cheque_number || null,
        application_copy: null,
        _method: 'post',
        // endorsements: this.loan?.endorsements || [],
      }, {
        resetOnSuccess: true
      });

      this.$page.props.errorBags['default'] = [];
    },
  },

  computed: {
    applicationCopyPreview() {
      return this.form.application_copy?.name || this.loan?.application_copy?.split("/").pop() || 'Choose file'
    }
  },

  watch: {
    show(val) {
      this.resetForm()
    },

    loan(val) {
      this.resetForm();
    }

  },
}
</script>
