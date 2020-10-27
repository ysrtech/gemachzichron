<template>
    <jet-authentication-card>

        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="password" value="Password" />
                <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <jet-button type="submit"class="ml-4" :processing="form.processing">
                    Confirm
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
