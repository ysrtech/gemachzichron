<template>
  <div class="rounded-md bg-white shadow max-w-3xl mx-auto">

    <update-profile-information-form :user="$page.props.user"/>

    <div class="border-t border-gray-200"></div>

    <update-password-form/>

    <div class="border-t border-gray-200"></div>

    <delete-user-form/>

  </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import DeleteUserForm from './Sections/DeleteUserForm'
import UpdatePasswordForm from './Sections/UpdatePasswordForm'
import UpdateProfileInformationForm from './Sections/UpdateProfileInformationForm'

export default {
  layout: (h, page) => h(AppLayout, {header: 'Profile'}, () => page),

  components: {
    DeleteUserForm,
    UpdatePasswordForm,
    UpdateProfileInformationForm,
  },
}
</script>
