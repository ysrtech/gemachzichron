<template>
  <div class="max-w-3xl mx-auto">

    <div class="mb-6 flex justify-between items-center px-1">
      <app-button @click="showCreateModal = true">
        <span>Create New User</span>
      </app-button>
    </div>

    <main class="flex-1 relative pb-8 z-0 overflow-y-auto mx-auto px-1">
      <div class="flex flex-col mt-2">
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
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
import UsersTable from "./UsersTable";
import UserFormModal from "./FormModal";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Users'}, () => page),

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
