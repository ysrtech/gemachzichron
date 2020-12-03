<template>
    <span>
      <app-form-modal :show="show || user" max-width="sm" @close="$emit('close')" @submitted="submitUser">

          <template #title>{{ title }}</template>

          <template #form>

            <div class="mb-3">
                <app-label for="name" value="Name"/>
                <app-input id="name" v-model="form.name"
                           class="mt-1 block w-full"
                           type="text"/>
                <app-input-error :message="form.error('name')" class="mt-1"/>
            </div>

            <div class="my-3" v-if="$page.props.user.role == roles.super">
                <app-label for="role" value="Role"/>
                <select id="role" v-model="form.role"
                        class="form-input rounded-md shadow-sm mt-1 block w-full">
                <option :value="roles.admin">Admin</option>
                <option :value="roles.super">Super Admin</option>
                </select>
                <app-input-error :message="form.error('role')" class="mt-1"/>
            </div>

            <div class="my-3">
                <app-label for="email" value="Email"/>
                <app-input id="email" v-model="form.email"
                           class="mt-1 block w-full"
                           type="email"/>
                <app-input-error :message="form.error('email')" class="mt-1"/>
            </div>

            <div class="my-3">
                <app-label for="password" value="Password"/>
                <app-input id="password" v-model="form.password"
                           class="mt-1 block w-full"
                           :placeholder="user ? 'Leave blank for no change' : ''"
                           type="password"/>
                <app-input-error :message="form.error('password')" class="mt-1"/>
            </div>

            <div class="my-3">
                <app-label for="password_confirmation" value="Confirm Password"/>
                <app-input id="password_confirmation" v-model="form.password_confirmation"
                           class="mt-1 block w-full"
                           type="password"/>
                <app-input-error :message="form.error('password_confirmation')" class="mt-1"/>
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
        this.form.put(this.$route('users.update', this.user.id), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      } else {
        this.form.post(this.$route('users.store'), {
          onSuccess: () => !this.form.hasErrors() ? this.$emit('close') : null
        })
      }

    },
  }
}
</script>
