<template>
    <jet-authentication-card>

        <jet-validation-errors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="mt-4">
                <jet-label for="password" value="Password" />
                <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox" name="remember" v-model="remember">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <inertia-link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Forgot your password?
                </inertia-link>

                <jet-button type="submit"class="ml-4" :processing="form.processing">
                    Login
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
            canResetPassword: Boolean,
            status: String
        },

        data() {
            return {
                remember: false,
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: ''
                })
            }
        },

        watch: {
            remember(value) {
                this.form.remember = value ? 'on' : ''
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('login'), {
                    onSuccess: () => {
                        this.remember = false
                    }
                })
            }
        }
    }
</script>
