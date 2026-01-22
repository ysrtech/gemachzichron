<template>
  <div class="min-h-screen flex flex-col justify-center items-center pt-0 bg-zinc-50">
    <inertia-head :title="title ? `${title} - ${APP_NAME}` : APP_NAME"/>
    <div class="w-full sm:max-w-md px-6 py-8 sm:bg-white sm:shadow-sm sm:ring-1 sm:ring-zinc-950/10 overflow-hidden sm:rounded-xl">
      <inertia-link href="/">
        <app-logo class="h-16 mx-auto my-6"/>
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
import AppLogo from "@/Components/Logo";
import AppSnackbar from "@/Components/Snackbar";
import AppBanner from "@/Components/Banner";
import {APP_NAME} from "@/config/app";
import AlertModal from "@/Components/AlertModal";
import LoginModal from "@/Components/LoginModal";
import ConfirmPasswordModal from "@/Components/ConfirmPasswordModal";
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
