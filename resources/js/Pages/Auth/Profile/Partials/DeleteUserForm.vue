<template>
  <app-action-section>
    <template #title>
      Delete Account
    </template>

    <template #description>
      Permanently delete your account.
    </template>

    <template #content>
      <div class="max-w-xl text-sm text-gray-600">
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your
        account, please download any data or information that you wish to retain.
      </div>

      <div class="mt-5">
        <app-button color="danger" @click="confirmUserDeletion">
          Delete Account
        </app-button>
      </div>

      <!-- Delete Account Confirmation Modal -->
      <app-dialog-modal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
        <template #title>
          Delete Account
        </template>

        <template #content>
          Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will
          be permanently deleted. Please enter your password to confirm you would like to permanently delete your
          account.

          <div class="mt-4">
            <app-input
              ref="password"
              v-model="form.password"
              :error="form.errors.password"
              class="mt-1 block w-3/4"
              placeholder="Password"
              type="password"
              @input="form.clearErrors('password')"
              @keyup.enter="deleteUser"/>
          </div>
        </template>

        <template #footer>
          <app-button color="secondary" @click="confirmingUserDeletion = false">
            Cancel
          </app-button>

          <app-button :processing="form.processing" class="ml-2" color="danger" @click="deleteUser">
            Delete Account
          </app-button>
        </template>
      </app-dialog-modal>

    </template>
  </app-action-section>
</template>

<script>
import AppActionSection from '@/Components/App/Sections/ActionSection'
import AppDialogModal from '@/Components/UI/Modals/DialogModal'

export default {
  components: {
    AppActionSection,
    AppDialogModal,
  },

  data() {
    return {
      confirmingUserDeletion: false,
      form: this.$inertia.form({
        password: '',
      }),
    }
  },

  watch: {
    confirmingUserDeletion(val) {
      this.form.clearErrors()
    }
  },

  methods: {
    confirmUserDeletion() {
      this.form.reset();

      this.confirmingUserDeletion = true;

      setTimeout(() => {
        this.$refs.password.focus()
      }, 250)
    },

    deleteUser() {
      this.form.delete(this.$route('profile.destroy'), {
        onSuccess: () => this.confirmingUserDeletion = false
      });
    },
  },
}
</script>
