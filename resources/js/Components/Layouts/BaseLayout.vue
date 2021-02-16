<template>
  <div>
    <app-banner
      v-if="banner"
      :options="$page.props.flash.banner"
      @close="$page.props.flash.banner = null"
    />

    <slot/>

    <app-snackbar
      v-if="snackbar"
      :options="$page.props.flash.snackbar"
      @close="$page.props.flash.snackbar = null"
    />

    <app-alert-modal
      v-if="alertModal"
      :options="$page.props.flash.modal"
      @close="$page.props.flash.modal = null"
    />

    <app-login-modal
      v-if="loginModal"
      :show="showLoginModal"
      @close="showLoginModal = false;"
    />
  </div>
</template>

<script>
import AppBanner from "@/Components/UI/Banner";
import AppSnackbar from "@/Components/UI/Snackbar";
import AppAlertModal from "@/Components/UI/Modals/AlertModal";
import AppLoginModal from "@/Components/App/LoginModal";

export default {
  components: {
    AppAlertModal,
    AppBanner,
    AppSnackbar,
    AppLoginModal,
  },

  data() {
    return {
      showLoginModal: false
    }
  },

  props: {
    alertModal: {
      default: true,
    },

    banner: {
      default: true
    },

    snackbar: {
      default: true
    },

    loginModal: {
      default: true
    },
  },

  created() {
    if (!this.loginModal) {
      return;
    }

    this.$inertia.on('invalid', (event) => {
      if (event.detail.response.status === 401) {
        event.preventDefault()
        this.showLoginModal = true
      }
    })
  },
}
</script>
