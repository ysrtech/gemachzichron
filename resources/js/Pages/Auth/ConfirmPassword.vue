<template>
  <app-auth-layout>

    <div class="mb-4 text-sm text-gray-600">
      This is a secure area of the application. Please confirm your password before continuing.
    </div>

    <form @submit.prevent="submit">
      <div>
        <app-input
          id="password"
          v-model="form.password"
          :error="form.errors.password"
          autocomplete="current-password"
          autofocus
          label="Password"
          required
          type="password"
          @update:model-value="form.clearErrors('password')"/>
      </div>

      <div class="flex items-center justify-end mt-5">
        <app-button :processing="form.processing" class="ml-4" type="submit">
          Confirm
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
        password: '',
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.$route('password.confirm'))
    }
  }
}
</script>
