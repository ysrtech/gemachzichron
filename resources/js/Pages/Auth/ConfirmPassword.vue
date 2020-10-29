<template>
    <app-authentication-card>

        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <app-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <app-label for="password" value="Password" />
                <app-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <app-button type="submit"class="ml-4" :processing="form.processing">
                    Confirm
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
                    password: '',
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.confirm'))
            }
        }
    }
</script>
