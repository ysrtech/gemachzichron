<template>
  <div class="md:h-screen md:flex md:flex-col bg-gray-100">

    <div class="flex justify-between h-16 flex-wrap">

      <div class="bg-gray-900 md:flex-shrink-0 w-full md:w-52 px-6 flex items-center justify-center h-16">

        <!-- Small Screen Nav -->
        <div class="absolute md:hidden left-6">
          <button
            class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-gray-100 focus:outline-none focus:text-gray-100 -ml-2"
            @click="showOverlayNavigation = true">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path class="inline-flex" d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            </svg>
          </button>

          <app-side-overlay
            :show="showOverlayNavigation"
            @close="showOverlayNavigation = false"
            width="w-52"
            background="bg-gray-900"
          >
            <app-nav @close-navigation="showOverlayNavigation = false"/>
          </app-side-overlay>

        </div>

        <!-- Logo -->
        <div>
          <inertia-link :href="$route('dashboard')">
            <app-logo class="h-14 mx-auto" color-class="text-gray-300"/>
          </inertia-link>
        </div>

      </div>

      <div class="flex-grow flex items-center justify-between h-16 px-4 md:px-6 lg:px-10 bg-white shadow border-b">


        <h2 id="header" class="font-medium text-2xl text-gray-800 leading-tight">
          <template v-if="header">{{ header }}</template>
        </h2>

        <!-- Settings Dropdown -->
        <div class="ml-6 flex items-center">
<!--            <user-notifications/>-->
          <user-settings-dropdown class="ml-3 relative"/>
        </div>
      </div>

    </div>

    <div class="md:flex md:flex-grow md:overflow-hidden">
      <div class="hidden md:block bg-gray-900 flex-shrink-0 w-52 py-6 px-3 overflow-y-auto">
        <app-nav></app-nav>
      </div>

      <!-- Page Content -->
      <main class="w-full md:overflow-y-auto" scroll-region>
        <div class="pt-24 md:pt-10 py-6 md:py-10 px-3 sm:px-6 lg:px-10">
          <slot/>
        </div>
      </main>
    </div>

    <partials/>
  </div>
</template>

<script>
import AppNav from "@/Components/App/AppNav";
import AppLogo from "@/Components/UI/Logo";
import AppSideOverlay from "@/Components/UI/SideOverlay";
import Partials from "@/Partials/partials";
import UserNotifications from "@/Components/App/UserNotifications";
import UserSettingsDropdown from "@/Components/App/UserSettingsDropdown";

export default {
  components: {
    UserSettingsDropdown,
    UserNotifications,
    Partials,
    AppSideOverlay,
    AppLogo,
    AppNav
  },

  props: {
    header: {
      type: String,
      default: ''
    }
  },

  data() {
    return {
      showOverlayNavigation: false,
    }
  }
}
</script>
