<template>
  <div class="bg-white shadow rounded-md px-6">

    <div class="py-4 flex justify-between">
      <div class="text-xl font-medium">Dependents</div>
      <button
        class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300"
        @click="addDependent = true">
        add
      </button>
    </div>

    <div class="pb-4">
      <div class="bg-white rounded border border-gray-200 overflow-x-auto">
        <table class="w-full whitespace-no-wrap text-sm">

          <tr class="text-left text-xs text-gray-400">
            <th class="px-4 py-3 font-medium">Name</th>
            <th class="px-4 py-3 font-medium" colspan="2">Date of Birth</th>
          </tr>

          <tr v-for="dependent in dependents" :key="dependent.id">

            <td class="border-t px-4 py-3" :title="dependent.hebrew_name">
              {{ dependent.first_name + ' ' + dependent.last_name }}
            </td>

            <td class="border-t px-4 py-3">{{ formatDate(dependent.dob) }}</td>

            <td class="border-t w-px p-0">
              <div class="px-2 flex items-center text-gray-500">
                <button
                  class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 hover:text-gray-900 focus:bg-gray-300"
                  @click="dependentToUpdate = dependent">
                  edit
                </button>
                <button
                  class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 hover:text-gray-900 focus:bg-gray-300"
                  @click="dependentToDelete = dependent">
                  delete
                </button>
              </div>
            </td>

          </tr>

          <tr v-if="dependents.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No dependents.</td>
          </tr>

        </table>
      </div>
    </div>

    <dependent-form-modal
      :member="member"
      :show="addDependent"
      :dependent="dependentToUpdate"
      @close="addDependent = false; dependentToUpdate = null;"
    />

    <delete-dependent-confirmation
      :dependent="dependentToDelete"
      @close="dependentToDelete = null"
    />

  </div>
</template>

<script>

import DependentFormModal from "./DependentFormModal";
import DeleteDependentConfirmation from "./DeleteDependentConfirmation";

export default {
  name: "DependentsCard",

  components: {
    DeleteDependentConfirmation,
    DependentFormModal,
  },

  data() {
    return {
      dependentToUpdate: null,
      addDependent: false,
      dependentToDelete: null
    }
  },

  props: {
    member: Object,
    dependents: Array
  },
}
</script>
