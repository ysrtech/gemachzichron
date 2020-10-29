<template>
  <span>

      <app-form-modal :show="show || dependent" @close="$emit('close')" @submitted="submit" max-width="sm">

          <template #title>{{ dependent ? 'Update Dependent' : 'Add Dependent' }}</template>

          <template #form>

              <div class="mb-4">
                  <app-label for="first_name" value="First Name"/>
                  <app-input id="first_name" v-model="form.first_name" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="text"/>
                  <app-input-error :message="form.error('first_name')" class="mt-1"/>
              </div>

              <div class="my-4">
                  <app-label for="last_name" value="Last Name"/>
                  <app-input id="last_name" v-model="form.last_name" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="text"/>
                  <app-input-error :message="form.error('last_name')" class="mt-1"/>
              </div>

              <div class="my-4">
                  <app-label for="hebrew_name" value="Hebrew Name"/>
                  <app-input id="hebrew_name" v-model="form.hebrew_name" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="text"/>
                  <app-input-error :message="form.error('hebrew_name')" class="mt-1"/>
              </div>

              <div class="my-4">
                  <app-label for="dob" value="Date of Birth"/>
                  <app-input id="wife_name" v-model="form.dob" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="date"/>
                  <app-input-error :message="form.error('dob')" class="mt-1"/>
              </div>

          </template>

          <template #footer>
              <app-button color="secondary" type="button" @click.native="$emit('close')">
                  Cancel
              </app-button>

              <app-button :processing="form.processing" class="ml-3" color="primary" type="submit">
                  {{ button }}
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
    member: Object,
    dependent: Object,

    button: {
      default: 'Submit',
    }
  },


  data() {
    return {
      form: this.refreshDependent(this.dependent)
    }
  },

  watch: {
    dependent(d) {
      this.form = this.refreshDependent(d)
    },
  },

  methods: {
    refreshDependent(dependent) {
      return this.$inertia.form({
        first_name: dependent?.first_name || '',
        last_name: dependent?.last_name || this.member?.last_name || '',
        hebrew_name: dependent?.hebrew_name || '',
        dob: dependent?.dob || ''
      }, {
        resetOnSuccess: true
      })
    },

    submit() {

      if (this.dependent) {
        this.form.put(route('dependents.update', this.dependent.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }
      else {
        this.form.post(route('members.dependents.store', this.member.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }
    },
  }
}
</script>
