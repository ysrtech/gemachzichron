<template>
  <div class="md:h-screen md:flex md:flex-col bg-zinc-50">
    <inertia-head :title="title ? `${title} - ${APP_NAME}` : APP_NAME"/>

    <div class="flex justify-between h-16 flex-wrap border-b border-zinc-950/10">

      <!-- Logo -->
      <div class="bg-zinc-900 md:shrink-0 w-full md:w-64 px-6 flex items-center justify-center h-16">
        <inertia-link :href="$route('dashboard')">
          <app-logo class="h-12 mx-auto" color-class="text-white"/>
        </inertia-link>
      </div>

      <!-- Toolbar -->
      <div class="grow flex items-center justify-between h-16 px-4 md:px-6 lg:px-8 bg-white">
        <h2 id="header" class="font-semibold text-xl text-zinc-900">
          {{ title }}
        </h2>

        <div class="ml-6 flex items-center">
          <user-settings-dropdown class="ml-3 relative"/>
        </div>
      </div>

    </div>


      <!-- Small Screen Nav -->
      <div class="absolute md:hidden top-4 left-4">
        <button
          class="inline-flex items-center justify-center p-2 text-zinc-500 hover:text-zinc-700 focus:outline-none rounded-md hover:bg-zinc-100"
          @click="mobileMenuOpen = true">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
          </svg>
        </button>

        <app-side-overlay
          :show="mobileMenuOpen"
          @close="mobileMenuOpen = false"
          width="w-64"
          background="bg-zinc-900"
        >
          <app-nav @item-clicked="mobileMenuOpen = false"/>
        </app-side-overlay>
      </div>

    <div class="md:flex md:grow md:overflow-hidden">
      <!-- Regular Screen Nav -->
      <div
        class="hidden md:block bg-zinc-900 shrink-0 w-64 py-6 overflow-y-auto scrollbar-thumb-rounded-full
         scrollbar-thumb-zinc-700 hover:scrollbar-thumb-zinc-600 scrollbar-w-2 scrollbar-zinc-900">
        <app-nav/>
      </div>

      <!-- Page Content -->
      <main class="w-full md:overflow-y-auto bg-zinc-50" scroll-region>
        <div class="pt-24 md:pt-8 py-6 md:py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
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
