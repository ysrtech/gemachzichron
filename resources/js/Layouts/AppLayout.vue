<template>
  <div class="md:h-screen md:flex md:flex-col bg-gray-100">
    <inertia-head :title="title ? `${title} - ${APP_NAME}` : APP_NAME"/>

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
          {{ title }}
        </h2>

        <!-- Settings Dropdown -->
        <div class="ml-6 flex items-center">
          <user-settings-dropdown class="ml-3 relative"/>
        </div>
      </div>

    </div>

    <div class="md:flex md:flex-grow md:overflow-hidden">
      <div
        class="hidden md:block bg-gray-900 flex-shrink-0 w-52 py-6 overflow-y-auto scrollbar-thumb-rounded-full
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

    <partials/>
  </div>
</template>

<script>
import AppNav from "@/Components/App/AppNav";
import AppLogo from "@/Components/UI/Logo";
import AppSideOverlay from "@/Components/UI/SideOverlay";
import Partials from "@/Partials/partials";
import UserSettingsDropdown from "@/Components/App/UserSettingsDropdown";
import {APP_NAME} from "@/config/app";
import {Inertia} from "@inertiajs/inertia";

export default {
  components: {
    UserSettingsDropdown,
    Partials,
    AppSideOverlay,
    AppLogo,
    AppNav
  },

  props: {
    title:  String,
  },

  data() {
    return {
      APP_NAME,
      showOverlayNavigation: false,
    }
  },
}
</script>
