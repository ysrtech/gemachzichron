<template>
  <app-auth-layout title="Verify Email">

    <div class="mb-4 text-sm text-gray-600">
      Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just
      emailed to you? If you didn't receive the email, we will gladly send you another.
    </div>

    <form @submit.prevent="submit">
      <div class="mt-4 flex items-center justify-start">
        <app-button :processing="form.processing" type="submit">
          Resend Verification Email
        </app-button>

        <inertia-link
          :href="$route('logout')"
          as="button"
          class="underline ml-4 text-sm text-gray-600 hover:text-gray-900 focus:outline-none"
          method="post"
        >
          Logout
        </inertia-link>
      </div>
    </form>

  </app-auth-layout>
</template>

<script>
import AppAuthLayout from '@/Layouts/AuthLayout'

export default {
  components: {
    AppAuthLayout,
  },

  data() {
    return {
      form: this.$inertia.form()
    }
  },

  methods: {
    submit() {
      this.form.post(this.$route('verification.send'))
    },
  }
}
</script>
