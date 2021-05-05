<template>
  <app-modal :show="show" max-width="md" @close="$emit('close')">
    <div>

      <div class="pb-3 pt-6 text-center text-4xl">
        Login
      </div>

      <form @submit.prevent="submit">
        <div class="px-8 pt-4 pb-5">
          <div>
            <app-input
              id="email"
              v-model="form.email"
              :error="form.errors.email"
              autofocus
              label="Email"
              required
              type="email"
              @input="form.clearErrors('email')"/>
          </div>

          <div class="mt-4">
            <app-input
              id="password"
              v-model="form.password"
              :error="form.errors.password"
              autocomplete="current-password"
              label="Password"
              required
              type="password"
              @input="form.clearErrors('password')">

              <template #top-right>
                <inertia-link
                  :href="$route('password.request')"
                  class="underline text-xs text-gray-600 hover:text-gray-900"
                >
                  Forgot your password?
                </inertia-link>

              </template>
            </app-input>
          </div>

          <div class="block mt-4">
            <label class="flex items-center">
              <app-checkbox v-model="form.remember" name="remember"/>
              <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
          </div>

        </div>

        <div class="px-8 py-4 bg-gray-100 flex items-center justify-end">

          <div class="flex items-center justify-end">
            <inertia-link :href="$route('register')" class="underline text-sm text-gray-600 hover:text-gray-900">
              Don't have an account?
            </inertia-link>

            <app-button :processing="form.processing" class="ml-4" type="submit">
              Login
            </app-button>
          </div>

        </div>
      </form>

    </div>


  </app-modal>

</template>

<script>
import AppModal from "@/Components/UI/Modal";
import AppCheckbox from "@/Components/UI/Checkbox";


export default {
  components: {
    AppModal,
    AppCheckbox
  },

  emits: ['close'],

  props: {
    show: {
      default: false
    }
  },

  watch: {
    show(val) {
      this.form.clearErrors()
    }
  },

  data() {
    return {
      form: this.$inertia.form({
        email: null,
        password: null,
        remember: false,
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.$route('login'), {
        onSuccess: () => {
          this.$emit('close')
          this.form.reset()
        },
      })
    }
  }
}
</script>
