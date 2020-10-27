<template>
  <span>

      <jet-form-modal :show="show || dependent" @close="$emit('close')" @submitted="submit" max-width="sm">

          <template #title>{{ dependent ? 'Update Dependent' : 'Add Dependent' }}</template>

          <template #form>

              <div class="mb-4">
                  <jet-label for="first_name" value="First Name"/>
                  <jet-input id="first_name" v-model="form.first_name" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="text"/>
                  <jet-input-error :message="form.error('first_name')" class="mt-1"/>
              </div>

              <div class="my-4">
                  <jet-label for="last_name" value="Last Name"/>
                  <jet-input id="last_name" v-model="form.last_name" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="text"/>
                  <jet-input-error :message="form.error('last_name')" class="mt-1"/>
              </div>

              <div class="my-4">
                  <jet-label for="hebrew_name" value="Hebrew Name"/>
                  <jet-input id="hebrew_name" v-model="form.hebrew_name" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="text"/>
                  <jet-input-error :message="form.error('hebrew_name')" class="mt-1"/>
              </div>

              <div class="my-4">
                  <jet-label for="dob" value="Date of Birth"/>
                  <jet-input id="wife_name" v-model="form.dob" autocomplete="blabla"
                             class="mt-1 block w-full"
                             type="date"/>
                  <jet-input-error :message="form.error('dob')" class="mt-1"/>
              </div>

          </template>

          <template #footer>
              <jet-button color="secondary" type="button" @click.native="$emit('close')">
                  Cancel
              </jet-button>

              <jet-button :processing="form.processing" class="ml-3" color="primary" type="submit">
                  {{ button }}
              </jet-button>
          </template>
      </jet-form-modal>
  </span>
</template>

<script>
import JetButton from '../../../Shared/Button'
import JetFormModal from '../../../Shared/FormModal'
import JetLabel from '../../../Shared/Label'
import JetInput from '../../../Shared/Input'
import JetInputError from '../../../Shared/InputError'


export default {

  components: {
    JetButton,
    JetFormModal,
    JetLabel,
    JetInput,
    JetInputError,
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
