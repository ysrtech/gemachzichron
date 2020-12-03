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
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
            </div>

            <div class="mt-5">
                <app-button color="danger" @click.native="confirmUserDeletion">
                    Delete Account
                </app-button>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <app-dialog-modal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
                <template #title>
                    Delete Account
                </template>

                <template #content>
                    Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.

                    <div class="mt-4">
                        <app-input type="password" class="mt-1 block w-3/4" placeholder="Password"
                                    ref="password"
                                    v-model="form.password"
                                    @keyup.enter.native="deleteUser" />

                        <app-input-error :message="form.error('password')" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <app-button color="secondary" @click.native="confirmingUserDeletion = false">
                        Cancel
                    </app-button>

                    <app-button color="danger" class="ml-2" @click.native="deleteUser" :processing="form.processing">
                        Delete Account
                    </app-button>
                </template>
            </app-dialog-modal>
        </template>
    </app-action-section>
</template>

<script>
    import AppActionSection from '../../../Shared/ActionSection'
    import AppDialogModal from '../../../Shared/DialogModal'
    import AppButton from '../../../Shared/Button'
    import AppInput from '../../../Shared/Input'
    import AppInputError from '../../../Shared/InputError'

    export default {
        components: {
            AppActionSection,
            AppButton,
            AppDialogModal,
            AppInput,
            AppInputError,
        },

        data() {
            return {
                confirmingUserDeletion: false,
                deleting: false,

                form: this.$inertia.form({
                    '_method': 'DELETE',
                    password: '',
                }, {
                    bag: 'deleteUser'
                })
            }
        },

        methods: {
            confirmUserDeletion() {
                this.form.password = '';

                this.confirmingUserDeletion = true;

                setTimeout(() => {
                    this.$refs.password.focus()
                }, 250)
            },

            deleteUser() {
                this.form.post(this.$route('current-user.destroy'), {
                  preserveScroll: true,
                  onSuccess: () => {
                    if (! this.form.hasErrors()) {
                      this.confirmingUserDeletion = false;
                    }
                  }
                })
            },
        },
    }
</script>
