<template>
  <span>

      <app-form-modal :show="show" @close="$emit('close')" @submitted="submitMember">

          <template #title>{{ title }}</template>

          <template #form>
              <div class="grid grid-cols-6 gap-6">

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="first_name" value="First Name"/>
                      <app-input id="first_name" v-model="form.first_name" autocomplete="off"
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <app-input-error :message="form.error('first_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="last_name" value="Last Name"/>
                      <app-input id="last_name" v-model="form.last_name" autocomplete="off"
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <app-input-error :message="form.error('last_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="hebrew_name" value="Hebrew Name"/>
                      <app-input id="hebrew_name" v-model="form.hebrew_name" autocomplete="off"
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <app-input-error :message="form.error('hebrew_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="wife_name" value="Wife's Name"/>
                      <app-input id="wife_name" v-model="form.wife_name" autocomplete="off"
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <app-input-error :message="form.error('wife_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="email" value="Email"/>
                      <app-input id="email" v-model="form.email" autocomplete="off" class="mt-1 block w-full"
                                 type="email"/>
                      <app-input-error :message="form.error('email')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="home_phone" value="Home Phone"/>
                      <app-input id="home_phone" v-model="form.home_phone" autocomplete="off" class="mt-1 block w-full"
                                 type="text"/>
                      <app-input-error :message="form.error('home_phone')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="mobile_phone" value="Cellphone"/>
                      <app-input id="mobile_phone" v-model="form.mobile_phone" autocomplete="off"
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <app-input-error :message="form.error('mobile_phone')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <app-label for="shtibel" value="Shtibel"/>
                      <app-input id="shtibel" v-model="form.shtibel" autocomplete="off" class="mt-1 block w-full"
                                 type="text"/>
                      <app-input-error :message="form.error('shtibel')" class="mt-1"/>
                  </div>

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
    title: {
      default: 'Create Member',
    },
    button: {
      default: 'Submit',
    }
  },


  data() {
    return {
      form: this.$inertia.form({
        first_name: this.member?.first_name || '',
        last_name: this.member?.last_name || '',
        hebrew_name: this.member?.hebrew_name || '',
        wife_name: this.member?.wife_name || '',
        email: this.member?.email || '',
        home_phone: this.member?.home_phone || '',
        mobile_phone: this.member?.mobile_phone || '',
        shtibel: this.member?.shtibel || '',
      }, {
        resetOnSuccess: false
      })
    }
  },

  methods: {
    submitMember() {
      if (this.member) {
        this.form.put(this.$route('members.update', this.member.id), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      } else {
        this.form.post(this.$route('members.store'), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }

    },
  }
}
</script>
