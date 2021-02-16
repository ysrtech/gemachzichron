<template>
  <div class="mx-auto max-w-6xl">

    <update-profile-information-form :user="$page.props.user"/>

    <div class="hidden sm:block py-8">
      <div class="border-t border-gray-200"></div>
    </div>

    <update-password-form class="mt-10 sm:mt-0"/>

<!--    <div class="hidden sm:block py-8">-->
<!--      <div class="border-t border-gray-200"></div>-->
<!--    </div>-->

<!--    <delete-user-form class="mt-10 sm:mt-0"/>-->

  </div>
</template>

<script>
import AppLayout from '@/Components/Layouts/AppLayout'
import DeleteUserForm from './Partials/DeleteUserForm'
import UpdatePasswordForm from './Partials/UpdatePasswordForm'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm'

export default {
  layout: (h, page) => h(AppLayout, {header: 'Profile'}, () => page),

  components: {
    AppLayout,
    DeleteUserForm,
    UpdatePasswordForm,
    UpdateProfileInformationForm,
  },
}
</script>
