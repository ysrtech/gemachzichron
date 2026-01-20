<template>
  <div class="w-full">

    <div class="mb-6 flex justify-between items-center px-1">
      <app-button @click="showCreateModal = true">
        <span>Create New User</span>
      </app-button>
    </div>

    <main class="flex-1 relative pb-8 z-0 overflow-y-auto px-1">
      <div class="flex flex-col mt-2">
        <div class="align-middle w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
          <users-table
            :users="users"
            @edit-user="userToEdit = $event; showCreateModal = true"
          />
        </div>
      </div>
    </main>

    <user-form-modal
      :user="userToEdit"
      :show="showCreateModal"
      @close="showCreateModal = false; userToEdit = null"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import UsersTable from "../../Components/App/Users/UsersTable";
import UserFormModal from "../../Components/App/Users/FormModal";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Users'}, () => page),

  components: {
    UserFormModal,
    UsersTable,
  },

  data() {
    return {
      showCreateModal: false,
      userToEdit: null
    }
  },

  props: {
    users: Object,
  },
}
</script>
