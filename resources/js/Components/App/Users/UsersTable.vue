<template>
  <div>
    <table class="min-w-full divide-y divide-gray-200">

      <thead>
      <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
        <th v-for="title in ['Name', 'Email', '']" class="px-6 py-3 font-medium">{{ title }}</th>
      </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
      <tr
        v-for="user in users.data"
        :key="user.id"
        class="bg-white text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">
        <td class="px-6 py-4 whitespace-nowrap">
          {{ user.name }}
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
          {{ user.email }}
        </td>

        <td class="px-6 whitespace-nowrap text-right">
          <button
            class="material-icons-outlined focus:outline-none rounded-full p-1 text-gray-500 hover:text-gray-700 hover:bg-gray-200 focus:bg-gray-300 mr-2"
            @click="$emit('edit-user', user)">
            edit
          </button>

          <button
            class="material-icons-outlined focus:outline-none rounded-full p-1 text-gray-500 hover:text-gray-700 hover:bg-gray-200 focus:bg-gray-300"
            @click="$page.props.flash.alert_modal = {
              icon: 'error',
              title: 'Delete User',
              message: `<div class='text-justify'>
                Are you sure you want to delete user <strong>${user.name || ''}</strong>?
                <div class='mt-2'></div>
                Once this user is deleted, all of its resources and data will be permanently deleted.
                Before deleting this user, please download any data or information that you wish to retain.
                </p>`,
              actionButton: {
                text: 'Delete',
                color: 'danger',
                route: $route('users.destroy', user.id),
                method: 'DELETE'
              },
              closeButton: { text: 'Cancel', color: 'secondary' },
            }">
            delete
          </button>
        </td>
      </tr>

      <tr v-if="users.total === 0">
        <td class="border-t px-6 py-10 text-center text-gray-500" colspan="3">No users found.</td>
      </tr>

      </tbody>
    </table>

    <!-- Pagination -->
    <div
      v-if="users.total > users.per_page"
      class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-300 sm:px-6">
      <app-pagination :response="users"/>
    </div>
  </div>
</template>

<script>
import AppDropdown from "@/Components/Dropdown";
import AppDropdownLink from "@/Components/DropdownLink";
import AppPagination from "@/Components/Pagination";

export default {
  components: {
    AppPagination,
    AppDropdownLink,
    AppDropdown
  },

  emits: ['edit-user'],

  props: {
    users: Object
  }
}
</script>
