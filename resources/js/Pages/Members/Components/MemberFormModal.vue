<template>
  <span>

      <jet-form-modal :show="show" @close="$emit('close')" @submitted="submitMember">

          <template #title>{{ title }}</template>

          <template #form>
              <div class="grid grid-cols-6 gap-6">

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="first_name" value="First Name"/>
                      <jet-input id="first_name" v-model="form.first_name" autocomplete="..."
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <jet-input-error :message="form.error('first_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="last_name" value="Last Name"/>
                      <jet-input id="last_name" v-model="form.last_name" autocomplete="..."
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <jet-input-error :message="form.error('last_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="hebrew_name" value="Hebrew Name"/>
                      <jet-input id="hebrew_name" v-model="form.hebrew_name" autocomplete="..."
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <jet-input-error :message="form.error('hebrew_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="wife_name" value="Wife's Name"/>
                      <jet-input id="wife_name" v-model="form.wife_name" autocomplete="..."
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <jet-input-error :message="form.error('wife_name')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="email" value="Email"/>
                      <jet-input id="email" v-model="form.email" autocomplete="..." class="mt-1 block w-full"
                                 type="email"/>
                      <jet-input-error :message="form.error('email')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="home_phone" value="Home Phone"/>
                      <jet-input id="home_phone" v-model="form.home_phone" autocomplete="..." class="mt-1 block w-full"
                                 type="text"/>
                      <jet-input-error :message="form.error('home_phone')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="mobile_phone" value="Cellphone"/>
                      <jet-input id="mobile_phone" v-model="form.mobile_phone" autocomplete="..."
                                 class="mt-1 block w-full"
                                 type="text"/>
                      <jet-input-error :message="form.error('mobile_phone')" class="mt-1"/>
                  </div>

                  <div class="col-span-6 sm:col-span-3">
                      <jet-label for="shtibel" value="Shtibel"/>
                      <jet-input id="shtibel" v-model="form.shtibel" autocomplete="..." class="mt-1 block w-full"
                                 type="text"/>
                      <jet-input-error :message="form.error('shtibel')" class="mt-1"/>
                  </div>

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
        this.form.put(route('members.update', this.member.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      } else {
        this.form.post(route('members.store').url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }

    },
  }
}
</script>
