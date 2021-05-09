<template>
  <div>
    <member-base :member="member">
      <div class="max-w-2xl mx-auto bg-white rounded-lg shadow divide-y divide-gray-200 overflow-hidden">
        <div class="p-4 sm:px-6 flex items-center justify-between">
          <h2 class="text-lg leading-6 font-medium text-gray-900">
            Children
          </h2>
          <button
            @click="openDependentModal = true"
            title="Add Child"
            class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
            add
          </button>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead v-if="member.dependents.length > 0">

          <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
            <th v-for="title in ['Name', 'Date of Birth', '']" class="px-6 py-3 font-medium">{{ title }}</th>
          </tr>

          </thead>
          <tbody class="bg-white divide-y divide-gray-200">

          <tr
            v-for="dependent in member.dependents"
            :key="dependent.id"
            class="bg-white text-sm text-gray-700">
            <td class="px-6 py-3.5 whitespace-nowrap space-x-2">
              <span>{{ dependent.name }}</span>
              <app-badge v-if="dependent.is_married" color="blue">Married</app-badge>
            </td>
            <td class="px-6 py-3.5 whitespace-nowrap">{{ date(dependent.dob) }}</td>
            <td class="pr-5 text-right w-px whitespace-nowrap text-gray-500 space-x-2">
              <button
                @click="openDependentModal = true; dependentToEdit = dependent"
                class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                edit
              </button>
              <button
                @click="deleteDependent(dependent)"
                class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                delete
              </button>
            </td>
          </tr>

          <tr v-if="member.dependents.length === 0">
            <td class="px-6 py-10 text-center text-gray-500" colspan="3">No Children Listed.</td>
          </tr>

          </tbody>
        </table>
      </div>
      <dependent-form-modal
        :show="openDependentModal"
        :member-id="member.id"
        :dependent="dependentToEdit"
        @close="openDependentModal = false; dependentToEdit = null"
      />
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import {date} from "@/helpers/dates";
import AppBadge from "@/Components/UI/Badge";
import DependentFormModal from "@/Pages/Members/Dependents/FormModal";

export default {
  layout: AppLayout,

  components: {
    DependentFormModal,
    AppBadge,
    MemberBase
  },

  data() {
    return {
      dependentToEdit: null,
      openDependentModal: false
    }
  },

  props: {
    member: Object,
  },

  methods: {
    date,
    deleteDependent(dependent) {
      if (confirm(`Are you sure you want to delete ${dependent?.name}?`)) {
        this.$inertia.delete(this.$route('dependents.destroy', dependent.id))
      }
    }
  }
}
</script>
