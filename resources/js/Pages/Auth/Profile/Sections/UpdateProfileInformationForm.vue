<template>
  <app-section>

    <template #title>
      Profile Information
    </template>

    <template #description>
      Update your account's profile information and email address.
    </template>

    <template #content>
      <form @submit.prevent="updateProfileInformation">
        <div class="grid grid-cols-6 gap-6">

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
      this.form.put(this.$route('profile.update'), {
        preserveScroll: true,
      });
    },
  },
}
</script>
