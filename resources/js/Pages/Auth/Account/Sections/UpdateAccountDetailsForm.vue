<template>
  <app-section>

    <template #title>
      Account Details
    </template>

    <template #description>
      Update your account name and email address.
    </template>

    <template #content>
      <form @submit.prevent="updateAccountDetails">
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
              @update:model-value="form.clearErrors('name')"
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
              @update:model-value="form.clearErrors('email')"
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
import AppInput from "@/Components/FormControls/Input"

export default {
  components: {
    AppInput,
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
    updateAccountDetails() {
      this.form.put(this.$route('account.update'), {
        preserveScroll: true,
      });
    },
  },
}
</script>
