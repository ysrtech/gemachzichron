<template>
  <div class="max-w-xl mx-auto">
    <div class="mb-6 flex justify-between items-center px-1">

      <app-button @click="openFormModal = true">
        <span>Create New Plan Type</span>
      </app-button>

    </div>

    <main class="flex-1 relative pb-8 z-0 overflow-y-auto mx-auto px-1">
      <div class="flex flex-col mt-2">
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">

            <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="planType in planTypes"
              :key="planType.id"
              class="bg-white text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ planType.name }}
              </td>

              <td class="px-6 whitespace-nowrap text-right">
                <button
                  class="material-icons-outlined focus:outline-none rounded-full p-1 text-gray-500 hover:text-gray-700 hover:bg-gray-200 focus:bg-gray-300 mr-2"
                  @click="openFormModal = true; planTypeToEdit = planType">
                  edit
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <plan-types-form-modal
      :plan-type="planTypeToEdit"
      :show=" openFormModal"
      @close=" openFormModal = false; planTypeToEdit = null"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import PlanTypesFormModal from "./FormModal";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Plan Types'}, () => page),

  components: {
    PlanTypesFormModal,
  },

  data() {
    return {
      openFormModal: false,
      planTypeToEdit: null
    }
  },

  props: {
    planTypes: Array,
  },
}
</script>
