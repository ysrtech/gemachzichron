<template>
  <div class="min-h-screen flex flex-col justify-center items-center pt-0 bg-gray-100">
    <inertia-head :title="title ? `${title} - ${APP_NAME}` : APP_NAME"/>
    <div class="w-full sm:max-w-md px-6 py-4 sm:bg-white sm:shadow-md overflow-hidden sm:rounded-lg">
      <inertia-link href="/">
        <app-logo class="h-24 mx-auto my-5"/>
      </inertia-link>
      <slot/>
    </div>

    <app-snackbar :show="showSnackbar" :message="$page.props.flash.snackbar?.message" @close="showSnackbar = false"/>
    <app-banner :show="showBanner" v-bind="$page.props.flash.banner" @close="showBanner = false"/>
    <alert-modal :show="showAlertModal" v-bind="$page.props.flash.alert_modal" @close="showAlertModal = false"/>
    <login-modal :show="showLoginModal" @close="showLoginModal = false"/>
    <confirm-password-modal :show="showConfirmPasswordModal" v-bind="$page.props.flash.confirm_password_modal" @close="showConfirmPasswordModal = false"/>

  </div>
</template>

<script>
import AppLogo from "@/Components/UI/Logo";
import AppSnackbar from "@/Components/UI/Snackbar";
import AppBanner from "@/Components/UI/Banner";
import {APP_NAME} from "@/config/app";
import AlertModal from "@/Components/App/AlertModal";
import LoginModal from "@/Components/App/LoginModal";
import ConfirmPasswordModal from "@/Components/App/ConfirmPasswordModal";
import {Head} from "@inertiajs/inertia-vue3";

export default {
  components: {
    ConfirmPasswordModal,
    AppLogo,
    LoginModal,
    AlertModal,
    AppBanner,
    AppSnackbar,
    'InertiaHead': Head
  },

  props: {
    title: String,
  },

  data() {
    return {
      APP_NAME,
      showSnackbar: false,
      showBanner: false,
      showAlertModal: false,
      showLoginModal: false,
      showConfirmPasswordModal: false,
    }
  },

  watch: {
    '$page.props.flash.snackbar'(snackbar) {
      if (snackbar) {
        this.showSnackbar = true
        setTimeout(() => this.showSnackbar = false, snackbar?.timeout || 300)
      } else {
        this.showSnackbar = false
      }
    },

    '$page.props.flash.banner'(banner) {
      this.showBanner = !!banner;
    },

    '$page.props.flash.alert_modal'(alertModal) {
      this.showAlertModal = !!alertModal;
    },

    '$page.props.flash.login_modal'(loginModal) {
      this.showLoginModal = !!loginModal;
    },

    '$page.props.flash.confirm_password_modal'(confirmPassword_Modal) {
      this.showConfirmPasswordModal = !!confirmPassword_Modal;
    },
  },

  created() {
    // render the flash component only - preserve current page (e.g. when returning Inertia::flash(...))
    this.$inertia.on('invalid', (event) => {
      if (event.detail.response.data?.flash) {
        event.preventDefault()
        this.$page.props.flash = event.detail.response.data.flash
      }
    })
  },
}
</script>
