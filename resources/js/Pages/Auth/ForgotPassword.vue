<template>
    <jet-authentication-card>

        <div class="mb-4 text-sm text-gray-600">
            Please enter your email address and we will email you a password reset link.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">

                <inertia-link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Back to login
                </inertia-link>

                <jet-button type="submit" class="ml-4" :processing="form.processing">
                    Email Password Reset Link
                </jet-button>
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
    import JetAuthenticationCard from '../../Shared/AuthenticationCard'
    import JetButton from '../../Shared/Button'
    import JetInput from '../../Shared/Input'
    import JetLabel from '../../Shared/Label'
    import JetValidationErrors from '../../Shared/ValidationErrors'

    export default {
        components: {
            JetAuthenticationCard,
            JetButton,
            JetInput,
            JetLabel,
            JetValidationErrors
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
                this.form.post(this.route('password.email'))
            }
        }
    }
</script>
