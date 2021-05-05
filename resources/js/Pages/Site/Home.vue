<template>
    <div class="absolute top-8 right-8">

      <template v-if="$page.props.user">
        <user-settings-dropdown class="ml-3 relative"/>
      </template>

      <div class="flex space-x-4" v-else>
        <button class="focus:outline-none text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center p-1 text-sm font-medium" @click="renderPartial('LoginModal')">
          Login
        </button>

<!--        <inertia-link :href="$route('register')" class="text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center p-1 text-sm font-medium">-->
<!--          Register-->
<!--        </inertia-link>-->
      </div>

    </div>
  <partials/>
</template>

<script>
import UserSettingsDropdown from "@/Components/App/UserSettingsDropdown";
import AppLogo from "@/Components/UI/Logo";
import RendersPartials from "@/Mixins/RendersPartials";
import Partials from "@/Partials/partials";

export default {
  components: {
    UserSettingsDropdown,
    AppLogo,
    Partials,
  },
  mixins: [RendersPartials],
}
</script>
