<template>
  <app-auth-layout title="Login">

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
          @update:model-value="form.clearErrors('email')"/>
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
          @update:model-value="form.clearErrors('password')">
        </app-input>
      </div>

      <div class="block mt-4">
        <label class="flex items-center">
          <app-checkbox v-model="form.remember" name="remember"/>
          <span class="ml-2 text-sm text-gray-600">Remember me</span>
        </label>
      </div>

      <div class="flex items-center justify-end mt-4">
        <inertia-link :href="$route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900">
          Forgot Password?
        </inertia-link>

        <app-button :processing="form.processing" class="ml-4" type="submit">
          Login
        </app-button>
      </div>
    </form>

  </app-auth-layout>
</template>

<script>
import AppAuthLayout from '@/Layouts/AuthLayout'
import AppCheckbox from "@/Components/FormControls/Checkbox"
import AppInput from "@/Components/FormControls/Input"

export default {
  components: {
    AppInput,
    AppAuthLayout,
    AppCheckbox,
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
      this.form.post(this.$route('login'))
    }
  }
}
</script>
