<template>
  <app-auth-layout>

    <form @submit.prevent="submit">
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
          autocomplete="new-password"
          label="Password"
          required
          type="password"
          @input="form.clearErrors('password')"/>
      </div>

      <div class="mt-4">
        <app-input
          id="password_confirmation"
          v-model="form.password_confirmation"
          :error="form.errors.password_confirmation"
          autocomplete="new-password"
          label="Confirm Password"
          required
          type="password"
          @input="form.clearErrors('password_confirmation')"/>
      </div>

      <div class="flex items-center justify-center sm:justify-end mt-5">
        <app-button :processing="form.processing" type="submit">
          Reset Password
        </app-button>
      </div>
    </form>

  </app-auth-layout>
</template>

<script>
import AppAuthLayout from '@/Components/Layouts/AuthLayout'

export default {
  components: {
    AppAuthLayout,
  },

  props: {
    email: String,
    token: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: '',
        password_confirmation: '',
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.$route('password.update'), {
        onSuccess: () => this.form.reset()
      });
    }
  }
}
</script>
