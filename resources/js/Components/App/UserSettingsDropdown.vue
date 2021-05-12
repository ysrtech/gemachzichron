<template>
  <app-dropdown align="right" width="36" v-if="$page.props.user">
    <template #trigger>
      <button
        class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
        <div>{{ $page.props.user.name }}</div>

        <div class="ml-1">
          <svg class="fill-current h-4 w-4" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" fill-rule="evenodd"/>
          </svg>
        </div>
      </button>
    </template>

    <template #content>

<!--      <app-dropdown-link :href="$route('dashboard')">-->
<!--        Dashboard-->
<!--      </app-dropdown-link>-->

      <app-dropdown-link :href="$route('account.show')">
        Profile
      </app-dropdown-link>

      <form @submit.prevent="$inertia.post($route('logout'))">
        <app-dropdown-link as="button">
          Logout
        </app-dropdown-link>
      </form>
    </template>
  </app-dropdown>
</template>

<script>
import AppDropdown from "@/Components/UI/Dropdown";
import AppDropdownLink from "@/Components/UI/DropdownLink";

export default {
  components: {
    AppDropdown,
    AppDropdownLink,
  },
}
</script>
