<template>
  <div class="md:h-screen md:flex md:flex-col bg-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="flex justify-between h-16 flex-wrap">

      <div
        class="bg-primary-900 md:flex-shrink-0 w-full md:w-56 px-6 flex items-center justify-between md:justify-center">

        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <inertia-link :href="route('dashboard')">
            <img src="/img/logo.png" alt="Logo" class="h-12 w-auto">
          </inertia-link>
        </div>

        <!-- Hamburger -->
        <div class="relative -mr-2 md:hidden px-4">
          <button @click="showingNavigationDropdown = ! showingNavigationDropdown"
                  class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-100 focus:outline-none focus:text-gray-100 transition duration-150 ease-in-out">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <!-- Full Screen Dropdown Overlay -->
          <div v-show="showingNavigationDropdown" class="fixed inset-0 z-40" @click="showingNavigationDropdown = false">
          </div>

          <transition
            enter-active-class="transition ease-out duration-200"
            enter-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-show="showingNavigationDropdown"
                 class="absolute z-50 mt-2 rounded-md shadow-lg w-48 right-0"
                 @click="showingNavigationDropdown = false">
              <div class="rounded-md shadow-xs bg-primary-800 p-4">
                <app-nav></app-nav>
              </div>
            </div>
          </transition>

        </div>
      </div>

      <div class="flex-grow flex items-center justify-between h-16 px-4 md:px-6 lg:px-8 bg-white shadow border-b">

        <h2 class="font-medium text-2xl text-gray-800 leading-tight">
          <template v-if="header">{{ header }}</template>
          <slot name="header" v-else></slot>
        </h2>


        <!-- Settings Dropdown -->
        <div class="ml-3 relative">
          <app-dropdown align="right" width="48">
            <template #trigger>
              <button v-if="$page.props.jetstream.managesProfilePhotos"
                      class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url"
                     :alt="$page.props.user.name"/>
              </button>

              <button v-else
                      class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                <div>{{ $page.props.user.name }}</div>

                <div class="ml-1">
                  <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"/>
                  </svg>
                </div>
              </button>
            </template>

            <template #content>
              <!-- Account Management -->
              <div class="block px-4 py-2 text-xs text-gray-400">
                Manage Account
              </div>

              <app-dropdown-link :href="route('profile.show')">
                Profile
              </app-dropdown-link>

              <div class="border-t border-gray-100"></div>

              <!-- Authentication -->
              <form @submit.prevent="logout">
                <app-dropdown-link as="button">
                  Logout
                </app-dropdown-link>
              </form>
            </template>
          </app-dropdown>
        </div>
      </div>

    </div>

    <div class="md:flex md:flex-grow md:overflow-hidden">
      <div class="hidden md:block bg-primary-800 flex-shrink-0 w-56 py-12 px-5 overflow-y-auto">
        <app-nav></app-nav>
      </div>

      <!-- Page Content -->
      <main class="w-full md:overflow-y-auto">
        <div class="mx-auto pt-24 md:py-10 px-3 sm:px-6 lg:px-8">
          <slot/>
        </div>
      </main>

    </div>

    <!-- Modal Portal -->
    <portal-target name="modal" multiple>
    </portal-target>
  </div>
</template>

<script>
import AppDropdown from '../Shared/Dropdown'
import AppDropdownLink from '../Shared/DropdownLink'
import AppNavLink from '../Shared/NavLink'
import AppResponsiveNavLink from '../Shared/ResponsiveNavLink'
import AppNav from "./AppNav";
import {EventBus} from "../app";

export default {
  components: {
    AppNav,
    AppDropdown,
    AppDropdownLink,
    AppNavLink,
    AppResponsiveNavLink,
  },

  data() {
    return {
      header: '',
      showingNavigationDropdown: false,
    }
  },

  created() {
    EventBus.$on('page-changed', header => this.header = header);
  },

  methods: {
    logout() {
      this.$inertia.post(route('logout'));
    },
  }
}
</script>
