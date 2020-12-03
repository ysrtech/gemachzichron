<template>
    <app-authentication-card>

        <app-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <app-label for="name" value="Name" />
                <app-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <app-label for="email" value="Email" />
                <app-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
            </div>

            <div class="mt-4">
                <app-label for="password" value="Password" />
                <app-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <app-label for="password_confirmation" value="Confirm Password" />
                <app-input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <inertia-link :href="$route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Already registered?
                </inertia-link>

                <app-button type="submit"class="ml-4" :processing="form.processing">
                    Register
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

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.$route('register'), {
                    onSuccess: () => {
                        this.form.password = ''
                        this.form.password_confirmation = ''
                    }
                })
            }
        }
    }
</script>
