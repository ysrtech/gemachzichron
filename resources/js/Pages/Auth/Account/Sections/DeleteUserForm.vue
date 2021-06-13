<template>
  <app-section>
    <template #title>
      Delete Account
    </template>

    <template #description>
      Permanently delete your account.
    </template>

    <template #content>
      <div class="bg-red-50 max-w-xl px-3.5 py-2.5 rounded-md text-red-700 text-sm">
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your
        account, please download any data or information that you wish to retain.
      </div>

      <div class="mt-5">
        <app-button color="danger" @click="deleteUser">
          Delete Account
        </app-button>
      </div>

    </template>
  </app-section>
</template>

<script>
import AppSection from './Section'

export default {
  components: {
    AppSection,
  },

  methods: {
    deleteUser() {
      this.$inertia.delete(this.$route('account.destroy'), {
        preserveScroll: true,
      })
    },
  },
}
</script>
