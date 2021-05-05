<template>
  <app-section>

    <template #title>
      Update Password
    </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #content>
      <form @submit.prevent="updatePassword">

        <div class="grid grid-cols-6 gap-6">

          <div class="col-span-6 sm:col-span-4">
            <app-input
              id="current_password"
              ref="current_password"
              v-model="form.current_password"
              :error="form.errors.current_password"
              autocomplete="current_password"
              label="Current Password"
              type="password"
              @input="form.clearErrors('current_password')"/>
          </div>

          <div class="col-span-6 sm:col-span-4">
            <app-input
              id="password"
              v-model="form.password"
              :error="form.errors.password"
              autocomplete="new-password"
              label="New Password"
              type="password"
              @input="form.clearErrors('password')"/>
          </div>

          <div class="col-span-6 sm:col-span-4">
            <app-input
              id="password_confirmation"
              v-model="form.password_confirmation"
              :error="form.errors.password_confirmation"
              autocomplete="new-password"
              label="Confirm Password"
              type="password"
              @input="form.clearErrors('password_confirmation')"/>
          </div>
        </div>

        <app-button class="mt-5" :processing="form.processing" type="submit">
          Save
        </app-button>

      </form>
    </template>

  </app-section>
</template>

<script>
import AppSection from "./Section";

export default {
  components: {
    AppSection,
  },

  data() {
    return {
      form: this.$inertia.form({
        current_password: '',
        password: '',
        password_confirmation: '',
      }),
    }
  },

  methods: {
    updatePassword() {
      this.form.put(this.$route('profile.password.update'), {
        preserveScroll: true,
        onSuccess: () => this.form.reset()
      });
    },
  },

}
</script>
