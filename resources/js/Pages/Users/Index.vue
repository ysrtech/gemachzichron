<template>
  <div>

    <div class="px-6">

      <div class="mb-6 flex justify-end items-center">

        <jet-button @click.native="showCreateUserModal = true">
          <span>Create</span>
          <span class="hidden md:inline pl-1"> User</span>
        </jet-button>

      </div>

      <users-table :users="users.data"></users-table>
      <pagination :links="users.links"/>
    </div>

    <user-form-modal :show="showCreateUserModal" @close="showCreateUserModal = false" />

  </div>
</template>

<script>
import AppLayout from '../../Layouts/AppLayout'
import Pagination from "../../Shared/Pagination";
import JetButton from "../../Shared/Button"
import UsersTable from "./Components/UsersTable";
import UserFormModal from "./Components/UserFormModal";

export default {
  layout: AppLayout,
  header: 'Users',
  components: {
    UserFormModal,
    UsersTable,
    AppLayout,
    Pagination,
    JetButton,
  },
  props: {
    users: Object,
  },

  data() {
    return {
      showCreateUserModal: false,
    }
  },

}
</script>
