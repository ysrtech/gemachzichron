<template>
  <div class="md:h-screen md:flex md:flex-col bg-gray-100">
    <inertia-head :title="title ? `${title} - ${APP_NAME}` : APP_NAME"/>

    <div class="flex justify-between h-16 flex-wrap">

      <!-- Logo -->
      <div class="bg-gray-900 md:flex-shrink-0 w-full md:w-52 px-6 flex items-center justify-center h-16">
        <inertia-link :href="$route('dashboard')">
          <app-logo class="h-14 mx-auto" color-class="text-gray-300"/>
        </inertia-link>
      </div>

      <!-- Toolbar -->
      <div class="flex-grow flex items-center justify-between h-16 px-4 md:px-6 lg:px-10 bg-white shadow border-b">
        <h2 id="header" class="font-medium text-2xl text-gray-800 leading-tight">
          {{ title }}
        </h2>

        <div class="ml-6 flex items-center">
          <user-settings-dropdown class="ml-3 relative"/>
        </div>
      </div>

    </div>


      <!-- Small Screen Nav -->
      <div class="absolute md:hidden top-3 left-5">
        <button
          class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-gray-100 focus:outline-none focus:text-gray-100 -ml-2"
          @click="mobileMenuOpen = true">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
          </svg>
        </button>

        <app-side-overlay
          :show="mobileMenuOpen"
          @close="mobileMenuOpen = false"
          width="w-52"
          background="bg-gray-900"
        >
          <app-nav @item-clicked="mobileMenuOpen = false"/>
        </app-side-overlay>
      </div>

    <div class="md:flex md:flex-grow md:overflow-hidden">
      <!-- Regular Screen Nav -->
      <div
        class="hidden md:block bg-gray-900 flex-shrink-0 w-52 py-4 overflow-y-auto scrollbar-thumb-rounded-full
         scrollbar-thumb-gray-400 hover:scrollbar-thumb-gray-500 scrollbar-w-2 scrollbar-gray-900">
        <app-nav/>
      </div>

      <!-- Page Content -->
      <main class="w-full md:overflow-y-auto" scroll-region>
        <div class="pt-24 md:pt-10 py-6 md:py-10 px-3 sm:px-6 lg:px-10">
          <slot/>
        </div>
      </main>
    </div>

    <app-snackbar :show="showSnackbar" :message="$page.props.flash.snackbar?.message" @close="showSnackbar = false"/>
    <app-banner :show="showBanner" v-bind="$page.props.flash.banner" @close="showBanner = false"/>
    <alert-modal :show="showAlertModal" v-bind="$page.props.flash.alert_modal" @close="showAlertModal = false"/>
    <login-modal :show="showLoginModal" @close="showLoginModal = false"/>
    <confirm-password-modal :show="showConfirmPasswordModal" v-bind="$page.props.flash.confirm_password_modal" @close="showConfirmPasswordModal = false"/>
  </div>
</template>

<script>
import AppNav from "@/Components/AppNav";
import AppLogo from "@/Components/Logo";
import AppSideOverlay from "@/Components/SideOverlay";
import UserSettingsDropdown from "@/Components/UserSettingsDropdown";
import {APP_NAME} from "@/config/app";
import AppSnackbar from "@/Components/Snackbar";
import AppBanner from "@/Components/Banner";
import AlertModal from "@/Components/AlertModal";
import LoginModal from "@/Components/LoginModal";
import ConfirmPasswordModal from "@/Components/ConfirmPasswordModal";
import {Head} from "@inertiajs/inertia-vue3";

export default {
  components: {
    ConfirmPasswordModal,
    LoginModal,
    AlertModal,
    AppBanner,
    AppSnackbar,
    UserSettingsDropdown,
    AppSideOverlay,
    AppLogo,
    AppNav,
    'InertiaHead': Head
  },

  props: {
    title: String,
  },

  data() {
    return {
      APP_NAME,
      mobileMenuOpen: false,
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
