<template>
  <div>

    <div>
      <update-profile-information-form :user="$page.props.user"/>

      <app-section-border/>

      <update-password-form class="mt-10 sm:mt-0"/>

      <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
        <app-section-border/>

        <two-factor-authentication-form class="mt-10 sm:mt-0"/>
      </div>

      <app-section-border/>

      <logout-other-browser-sessions-form :sessions="sessions" class="mt-10 sm:mt-0"/>

      <app-section-border/>

      <delete-user-form class="mt-10 sm:mt-0"/>
    </div>

  </div>
</template>

<script>
import AppLayout from './../../Layouts/AppLayout'
import DeleteUserForm from './Components/DeleteUserForm'
import AppSectionBorder from '../../Shared/SectionBorder'
import LogoutOtherBrowserSessionsForm from './Components/LogoutOtherBrowserSessionsForm'
import TwoFactorAuthenticationForm from './Components/TwoFactorAuthenticationForm'
import UpdatePasswordForm from './Components/UpdatePasswordForm'
import UpdateProfileInformationForm from './Components/UpdateProfileInformationForm'

export default {
  layout: AppLayout,
  header: 'Profile',

  props: ['sessions'],

  components: {
    AppLayout,
    DeleteUserForm,
    AppSectionBorder,
    LogoutOtherBrowserSessionsForm,
    TwoFactorAuthenticationForm,
    UpdatePasswordForm,
    UpdateProfileInformationForm,
  },
}
</script>
