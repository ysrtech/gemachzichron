<template>
  <app-layout>
    <template #header>
      <h2 class="font-medium text-2xl text-gray-800 leading-tight">
        Profile
      </h2>
    </template>

    <div>
      <update-profile-information-form :user="$page.props.user"/>

      <jet-section-border/>

      <update-password-form class="mt-10 sm:mt-0"/>

      <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
        <jet-section-border/>

        <two-factor-authentication-form class="mt-10 sm:mt-0"/>
      </div>

      <jet-section-border/>

      <logout-other-browser-sessions-form :sessions="sessions" class="mt-10 sm:mt-0"/>

      <jet-section-border/>

      <delete-user-form class="mt-10 sm:mt-0"/>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from './../../Layouts/AppLayout'
import DeleteUserForm from './Components/DeleteUserForm'
import JetSectionBorder from '../../Shared/SectionBorder'
import LogoutOtherBrowserSessionsForm from './Components/LogoutOtherBrowserSessionsForm'
import TwoFactorAuthenticationForm from './Components/TwoFactorAuthenticationForm'
import UpdatePasswordForm from './Components/UpdatePasswordForm'
import UpdateProfileInformationForm from './Components/UpdateProfileInformationForm'

export default {
  props: ['sessions'],

  components: {
    AppLayout,
    DeleteUserForm,
    JetSectionBorder,
    LogoutOtherBrowserSessionsForm,
    TwoFactorAuthenticationForm,
    UpdatePasswordForm,
    UpdateProfileInformationForm,
  },
}
</script>
