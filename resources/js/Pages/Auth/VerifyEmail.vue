<template>
    <app-authentication-card>

        <div class="mb-4 text-sm text-gray-600">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent" >
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <app-button type="submit" :processing="form.processing">
                    Resend Verification Email
                </app-button>

                <inertia-link :href="route('logout')" method="post" class="underline text-sm text-gray-600 hover:text-gray-900">Logout</inertia-link>
            </div>
        </form>
    </app-authentication-card>
</template>

<script>
    import AppAuthenticationCard from '../../Shared/AuthenticationCard'
    import AppButton from '../../Shared/Button'

    export default {
        components: {
            AppAuthenticationCard,
            AppButton,
        },

        props: {
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: false,
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('verification.send'))
            },
        },

        computed: {
            verificationLinkSent() {
                return this.status === 'verification-link-sent';
            }
        }
    }
</script>
