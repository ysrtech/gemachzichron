<template>
  <span>

      <app-form-modal :show="show" @close="$emit('close')" @submitted="submitMembership" max-width="sm">

          <template #title>{{ title }}</template>

          <template #form>

             <div>
                  <app-label for="type" value="Membership Type"/>
                  <select id="type" v-model="form.type"
                          class="form-input rounded-md shadow-sm mt-1 block w-full">
                    <option :selected="membership && membership.type === 'Membership'">Membership</option>
                    <option :selected="membership && membership.type === 'Pekudon'">Pekudon</option>
                  </select>
                  <app-input-error :message="form.error('type')" class="mt-1"/>
              </div>

              <div class="my-3" v-if="form.type === 'Membership'">
                  <app-label for="plan_type_id" value="Plan Type"/>
                  <select id="plan_type_id" v-model="form.plan_type_id"
                          class="form-input rounded-md shadow-sm mt-1 block w-full">
                    <option
                      v-for="planType of planTypes"
                      :key="planType.id"
                      :value="planType.id"
                      :selected="membership && membership.plan_type_id === planType.id">
                      {{ planType.name }}
                    </option>
                  </select>
                  <app-input-error :message="form.error('plan_type_id')" class="mt-1"/>
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
    membership: Object,
    member: Object,
  },


  data() {

    return {
      planTypes: [],
      membershipTypes: {
        membership: 'Membership',
        pekudon: 'Pekudon'
      },
      title: this.membership ? 'Edit Membership' : 'Create Membership',
      form: this.$inertia.form()
    }
  },

  methods: {

    submitMembership() {
      if (this.membership) {
        this.form.put(route('memberships.update', this.membership.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      } else {
        this.form.post(route('members.memberships.store', this.member.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }
    },
  },

  watch: {
    show(val) {
      this.form = this.$inertia.form({
        type: this.membership?.type || null,
        plan_type_id: this.membership?.plan_type_id || null,
      })
    }
  },

  created() {
    // todo fix so it shouldn't call on every usage
    axios.get(route('plan-types.index').url())
      .then(response => {
        this.planTypes = response.data.plan_types
      })
  }
}
</script>
