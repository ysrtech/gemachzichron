<template>
    <span>
      <jet-form-modal :show="show || user" max-width="sm" @close="$emit('close')" @submitted="submitUser">

          <template #title>{{ title }}</template>

          <template #form>

            <div class="mb-3">
                <jet-label for="name" value="Name"/>
                <jet-input id="name" v-model="form.name"
                           class="mt-1 block w-full"
                           type="text"/>
                <jet-input-error :message="form.error('name')" class="mt-1"/>
            </div>

            <div class="my-3" v-if="$page.props.user.role == roles.super">
                <jet-label for="role" value="Role"/>
                <select id="role" v-model="form.role"
                        class="form-input rounded-md shadow-sm mt-1 block w-full">
                <option :value="roles.admin">Admin</option>
                <option :value="roles.super">Super Admin</option>
                </select>
                <jet-input-error :message="form.error('role')" class="mt-1"/>
            </div>

            <div class="my-3">
                <jet-label for="email" value="Email"/>
                <jet-input id="email" v-model="form.email"
                           class="mt-1 block w-full"
                           type="email"/>
                <jet-input-error :message="form.error('email')" class="mt-1"/>
            </div>

            <div class="my-3">
                <jet-label for="password" value="Password"/>
                <jet-input id="password" v-model="form.password"
                           class="mt-1 block w-full"
                           :placeholder="user ? 'Leave blank for no change' : ''"
                           type="password"/>
                <jet-input-error :message="form.error('password')" class="mt-1"/>
            </div>

            <div class="my-3">
                <jet-label for="password_confirmation" value="Confirm Password"/>
                <jet-input id="password_confirmation" v-model="form.password_confirmation"
                           class="mt-1 block w-full"
                           type="password"/>
                <jet-input-error :message="form.error('password_confirmation')" class="mt-1"/>
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
    user: Object,
    title: {
      default: 'Create User',
    },
    button: {
      default: 'Submit',
    }
  },


  data() {
    return {
      form: this.refreshForm(),
      roles: {
        admin: 1,
        super: 2,
      }
    }
  },

  watch: {
    user(user) {
      this.form = this.refreshForm(user)
    },
  },

  methods: {
    refreshForm() {
      return this.$inertia.form({
        name: this.user?.name || '',
        email: this.user?.email || '',
        role: this.user?.role || '',
        password: '',
        password_confirmation: '',
      }, {
        resetOnSuccess: true
      });
    },

    submitUser() {
      if (this.user) {
        this.form.put(route('users.update', this.user.id).url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      } else {
        this.form.post(route('users.store').url(), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }

    },
  }
}
</script>
