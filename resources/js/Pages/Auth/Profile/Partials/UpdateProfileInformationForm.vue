<template>
  <app-form-section @submitted="updateProfileInformation">

    <template #title>
      Profile Information
    </template>

    <template #description>
      Update your account's profile information and email address.
    </template>

    <template #form>
      <!-- Name -->
      <div class="col-span-6 sm:col-span-4">
        <app-input
          id="name"
          v-model="form.name"
          :error="form.errors.name"
          autocomplete="name"
          label="Name"
          type="text"
          @input="form.clearErrors('name')"
        />
      </div>

      <!-- Email -->
      <div class="col-span-6 sm:col-span-4">
        <app-input
          id="email"
          v-model="form.email"
          :error="form.errors.email"
          autocomplete="email"
          label="Email"
          type="email"
          @input="form.clearErrors('email')"
        />
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

  props: ['user'],

  data() {
    return {
      form: this.$inertia.form({
        name: this.user.name,
        email: this.user.email,
      }),
    }
  },

  methods: {
    updateProfileInformation() {
      this.form.put(this.$route('profile.update'), {});
    },
  },
}
</script>
