<template>
    <app-authentication-card>

        <div class="mb-4 text-sm text-gray-600">
            Please enter your email address and we will email you a password reset link.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <app-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <app-label for="email" value="Email" />
                <app-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">

                <inertia-link :href="$route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Back to login
                </inertia-link>

                <app-button type="submit" class="ml-4" :processing="form.processing">
                    Email Password Reset Link
                </app-button>
            </div>
        </form>
    </app-authentication-card>
</template>

<script>
    import AppAuthenticationCard from '../../Shared/AuthenticationCard'
    import AppButton from '../../Shared/Button'
    import AppInput from '../../Shared/Input'
    import AppLabel from '../../Shared/Label'
    import AppValidationErrors from '../../Shared/ValidationErrors'

    export default {
        components: {
            AppAuthenticationCard,
            AppButton,
            AppInput,
            AppLabel,
            AppValidationErrors
        },

        props: {
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: ''
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.$route('password.email'))
            }
        }
    }
</script>
