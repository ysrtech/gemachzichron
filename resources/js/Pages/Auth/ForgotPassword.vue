<template>
  <app-auth-layout title="Forgot Password">

    <div class="mb-4 text-sm text-gray-600">
      Please enter your email address and we will email you a password reset link.
    </div>

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
          @update:model-value="form.clearErrors('email')"
        />
      </div>

      <div class="flex items-center justify-end mt-5">
        <inertia-link :href="$route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
          Back to login
        </inertia-link>

        <app-button :processing="form.processing" class="ml-4" type="submit">
          Email Password Reset Link
        </app-button>
      </div>

    </form>
  </app-auth-layout>
</template>

<script>
import AppAuthLayout from '@/Layouts/AuthLayout'
import AppInput from "@/Components/FormControls/Input"

export default {
  components: {
    AppInput,
    AppAuthLayout,
  },

  data() {
    return {
      form: this.$inertia.form({
        email: ''
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.$route('password.email'));
    }
  }
}
</script>
