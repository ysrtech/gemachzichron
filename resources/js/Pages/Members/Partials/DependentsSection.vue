<template>
  <div class="w-full">

    <div class="flex space-x-1 items-center">
      <h3 class="text-xl font-medium leading-6 text-gray-900 mx-1 py-1.5">Dependents</h3>
      <button
        title="Add Dependent"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
        @click="openDependentModal = true">
        add
      </button>
    </div>

    <div class="bg-white rounded-lg shadow w-full mt-2 mx-1 overflow-x-auto overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead v-if="member.dependents.length > 0">

          <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
            <th class="px-6 py-3 font-medium">Name</th>
            <th colspan="2" class="py-3 font-medium">Date of Birth</th>
          </tr>

        </thead>
        <tbody class="bg-white divide-y divide-gray-200">

          <tr
            v-for="dependent in member.dependents"
            :key="dependent.id"
            class="bg-white text-sm text-gray-700">
            <td class="px-6 py-3.5 whitespace-nowrap">
              {{ dependent.first_name + ' ' + dependent.last_name }}
            </td>
            <td class="pr-6 py-3.5 whitespace-nowrap">{{ dependent.dob }}</td>
            <td class="pr-5 text-right w-px whitespace-nowrap text-gray-500">
              <button
                @click="openDependentModal = true; dependentToEdit = dependent"
                class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                edit
              </button>
              <button
                @click="dependentToDelete = dependent"
                class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                delete
              </button>
            </td>
          </tr>

          <tr v-if="member.dependents.length === 0">
            <td class="px-6 py-10 text-center text-gray-500" colspan="3">No dependents.</td>
          </tr>

        </tbody>
      </table>
    </div>

    <dependent-modal
      :member="member"
      :show="openDependentModal"
      :dependent="dependentToEdit"
      @close="openDependentModal = false; dependentToEdit = null"
    />

    <alert-modal
      :show="!!dependentToDelete"
      @close="dependentToDelete = null"
      :options="{
        icon: 'error',
        title: 'Delete Dependent',
        message: `Are you sure you want to delete dependent <strong>${dependentToDelete?.first_name} ${dependentToDelete?.last_name}</strong>?`,
        action_text: 'Delete',
        action_color: 'danger',
        action_route: 'dependents.destroy',
        action_route_params: dependentToDelete?.id || '0',
        action_method: 'DELETE',
        close_text: 'Cancel'
      }"/>

  </div>
</template>

<script>
import DependentModal from "@/Pages/Members/Partials/DependentModal";
import AlertModal from "@/Components/UI/Modals/AlertModal";

export default {

  components: {
    AlertModal,
    DependentModal,
  },

  data() {
    return {
      openDependentModal: false,
      dependentToEdit: null,
      dependentToDelete: null,
    }
  },

  props: {
    member: Object,
  },
}
</script>
