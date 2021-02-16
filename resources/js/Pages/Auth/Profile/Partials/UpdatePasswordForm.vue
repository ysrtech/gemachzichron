<template>
  <app-form-section @submitted="updatePassword">

    <template #title>
      Update Password
    </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #form>
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
    </template>

    <template #actions>
      <app-button :processing="form.processing" type="submit">
        Save
      </app-button>
    </template>

  </app-form-section>
</template>

<script>
import AppFormSection from '@/Components/App/Sections/FormSection'

export default {
  components: {
    AppFormSection,
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
      this.form.put(this.$route('profile.update-password'), {
        preserveScroll: true,
        onSuccess: () => this.form.reset()
      });
    },
  },

}
</script>
