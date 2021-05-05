<template>
  <nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">

        <!-- Nav left side -->
        <div class="flex">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <inertia-link :href="$route('home')" class="mr-3">
              <app-logo class="h-8 mx-auto" color-class="text-gray-500"/>
            </inertia-link>
          </div>

          <!-- Nav Items -->
          <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
            <template v-for="navItem in navItems">
              <inertia-link :href="$route(navItem.route)"
                            class=" inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                            :class="navItem.route === currentRouteName ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                {{ navItem.title }}
              </inertia-link>
            </template>
          </div>

          <!-- Mobile menu button -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none" @click="mobileMenuOpen = !mobileMenuOpen">
              <span class="sr-only">Open main menu</span>
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="mobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Nav right side -->
        <div class="ml-6 flex items-center">
          <template v-if="$page.props.user">
            <!--            <user-notifications/>-->
            <user-settings-dropdown class="ml-3 relative"/>
          </template>

          <div class="-my-px ml-6 flex space-x-4" v-else>
            <button class="focus:outline-none text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center p-1 text-sm font-medium" @click="renderPartial('LoginModal')">
              Login
            </button>

            <inertia-link :href="$route('register')" class="text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center p-1 text-sm font-medium">
              Register
            </inertia-link>
          </div>
        </div>

      </div>
    </div>

    <!-- Mobile view nav items -->
    <div class="sm:hidden" v-show="mobileMenuOpen">
      <div class="pt-2 pb-3 space-y-1">
        <template v-for="navItem in navItems">
          <inertia-link :href="$route(navItem.route)"
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                        :class="navItem.route === currentRouteName ? 'bg-primary-50 border-primary-500 text-primary-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'">
            {{ navItem.title }}
          </inertia-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script>
import AppLogo from "@/Components/UI/Logo";
import UserSettingsDropdown from "@/Components/App/UserSettingsDropdown";
import UserNotifications from "@/Components/App/UserNotifications";
import RendersPartials from "@/Mixins/RendersPartials";

export default {
  components: {
    UserNotifications,
    UserSettingsDropdown,
    AppLogo,
  },

  mixins: [RendersPartials],

  data() {
    return {
      mobileMenuOpen: false,
      navItems: [
        {
          title: "Home",
          route: "home",
        },
        {
          title: 'About',
          route: "login"
        },
        {
          title: "Features",
          route: "login",
        },
        {
          title: 'Pricing',
          route: "login"
        }
      ],
      currentRouteName: this.$route().current()
    }
  },

  watch: {
    '$page.url': function () {
      this.currentRouteName = this.$route().current()
    }
  }
}
</script>
