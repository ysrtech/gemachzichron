<template>
  <div class="rounded-md bg-white shadow max-w-3xl mx-auto">

    <update-account-details-form :user="$page.props.user"/>

    <div class="border-t border-gray-200"></div>

    <update-password-form/>

<!--    <div class="border-t border-gray-200"></div>-->

<!--    <delete-user-form/>-->

  </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import DeleteUserForm from './Sections/DeleteUserForm'
import UpdatePasswordForm from './Sections/UpdatePasswordForm'
import UpdateAccountDetailsForm from './Sections/UpdateAccountDetailsForm'

export default {
  layout: (h, page) => h(AppLayout, {title: 'Account'}, () => page),

  components: {
    DeleteUserForm,
    UpdatePasswordForm,
    UpdateAccountDetailsForm,
  },
}
</script>
