<template>
  <app-auth-layout>
    <form @submit.prevent="submit">

      <div>
        <app-input
          id="name"
          v-model="form.name"
          :error="form.errors.name"
          autocomplete="name"
          autofocus
          label="Name"
          required
          type="text"
          @input="form.clearErrors('name')"/>
      </div>

      <div class="mt-4">
        <app-input
          id="email"
          v-model="form.email"
          :error="form.errors.email"
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

      <div class="flex items-center justify-end mt-5">
        <inertia-link :href="$route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
          Already have an account?
        </inertia-link>

        <app-button :processing="form.processing" class="ml-4" type="submit">
          Register
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

  data() {
    return {
      form: this.$inertia.form({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.$route('register'), {
        onSuccess: () => this.form.reset()
      });
    }
  }
}
</script>
