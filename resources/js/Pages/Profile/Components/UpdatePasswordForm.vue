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
                <app-label for="current_password" value="Current Password" />
                <app-input id="current_password" type="password" class="mt-1 block w-full" v-model="form.current_password" ref="current_password" autocomplete="current-password" />
                <app-input-error :message="form.error('current_password')" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <app-label for="password" value="New Password" />
                <app-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" autocomplete="new-password" />
                <app-input-error :message="form.error('password')" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <app-label for="password_confirmation" value="Confirm Password" />
                <app-input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" autocomplete="new-password" />
                <app-input-error :message="form.error('password_confirmation')" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <app-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </app-action-message>

            <app-button type="submit" :processing="form.processing">
                Save
            </app-button>
        </template>
    </app-form-section>
</template>

<script>
    import AppActionMessage from '../../../Shared/ActionMessage'
    import AppButton from '../../../Shared/Button'
    import AppFormSection from '../../../Shared/FormSection'
    import AppInput from '../../../Shared/Input'
    import AppInputError from '../../../Shared/InputError'
    import AppLabel from '../../../Shared/Label'

    export default {
        components: {
            AppActionMessage,
            AppButton,
            AppFormSection,
            AppInput,
            AppInputError,
            AppLabel,
        },

        data() {
            return {
                form: this.$inertia.form({
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                }, {
                    bag: 'updatePassword',
                }),
            }
        },

        methods: {
            updatePassword() {
                this.form.put(this.$route('user-password.update'), {
                    preserveScroll: true,
                    onSuccess: () => {
                      this.$refs.current_password.focus()
                    }
                })
            },
        },
    }
</script>
