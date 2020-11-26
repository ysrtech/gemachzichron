<template>
  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full whitespace-no-wrap">

      <tr class="text-left text-xs text-gray-400 uppercase">
        <th class="px-6 pt-6 pb-4 font-medium">Name</th>
        <th class="px-6 pt-6 pb-4 font-medium">Email</th>
        <th class="px-6 pt-6 pb-4 font-medium">Actions</th>
      </tr>

      <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-for="user in users" :key="user.id">
        <td class="border-t px-6 py-4 flex items-center focus:text-primary-500">
            <span>{{ user.name }}</span>
            <div
              v-if="user.role == roles.super"
              class="bg-green-100 rounded-full flex items-center text-xs ml-2 px-2">
              <span>Super admin</span>
            </div>
        </td>

        <td class="border-t">{{ user.email }}</td>

        <td class="border-t w-px ">
          <div class="p-0 flex items-center justify-end">
            <div class="px-2">
              <button
                class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
                @click="userToUpdate = user">
                edit
              </button>
            </div>

            <div class="px-2">
              <inertia-link
                class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
                as="button"
                @click.prevent="confirmDeleteUser(user)"
                href="#">
                delete
              </inertia-link>
            </div>
          </div>
        </td>

      </tr>

    </table>

    <user-form-modal :user="userToUpdate" @close="userToUpdate = null" />

  </div>

</template>

<script>
import AppDropdown from "../../../Shared/Dropdown";
import AppDropdownLink from "../../../Shared/DropdownLink";
import UserFormModal from "./UserFormModal";

export default {
  name: "UsersTable",

  components: {
    UserFormModal,

    AppDropdown,
    AppDropdownLink,

  },

  data() {
    return {
      form: this.$inertia.form(),
      userToUpdate: null,
      roles: {
        admin: 1,
        super: 2,
      }
    }
  },

  props: {
    users: Array
  },

  methods: {
    confirmDeleteUser(user) {
      if(confirm('Are you sure you want to delete ' + user.name + '?')) {
        this.$inertia.delete(route('users.destroy', user.id))
      }
    }
  }

}
</script>
