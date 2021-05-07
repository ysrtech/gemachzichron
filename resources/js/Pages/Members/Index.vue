<template>
  <div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center px-1">
      <search-filter v-model="filterForm.search" class="w-full max-w-md mr-4" @reset="reset">
        <div class="p-4">
          <label class="block text-gray-700 text-xs">Archived:</label>
          <select v-model="filterForm.archived" class="mt-1 w-full text-sm border focus:outline-none rounded p-1">
            <option :value="null">Without Archived</option>
            <option value="with">With Archived</option>
            <option value="only">Only Archived</option>
          </select>
        </div>
      </search-filter>

      <div class="flex space-x-3">
        <app-button @click="showCreateModal = true">New Member</app-button>
        <a :href="$route('members.export')" download>
          <app-button color="secondary">Export Members</app-button>
        </a>
      </div>

    </div>

    <main class="flex-1 relative pb-8 z-0 overflow-y-auto mx-auto px-1">
      <div class="flex flex-col mt-2">
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
          <members-table
            :members="members"
            @edit-member="memberToEdit = $event; showCreateModal = true"
          />
        </div>
      </div>
    </main>

    <member-modal
      :member="memberToEdit"
      :show="showCreateModal"
      @close="showCreateModal = false; memberToEdit = null"
    />
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {mapValues, pickBy, throttle} from "lodash";
import SearchFilter from "@/Components/App/SearchFilter";
import MembersTable from "./Sections/MembersTable";
import MemberModal from "./Sections/MemberModal";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Members'}, () => page),

  components: {
    MemberModal,
    MembersTable,
    SearchFilter
  },

  data() {
    return {
      filterForm: {
        search: this.filters.search,
        archived: this.filters.archived,
      },

      showCreateModal: false,
      memberToEdit: null
    }
  },

  props: {
    members: Object,
    filters: Object,
  },

  watch: {
    filterForm: {
      handler: throttle(function () {
        let query = pickBy(this.filterForm)
        this.$inertia.get(
          this.$route('members.index'),
          Object.keys(query).length ? query : {},
          {
            preserveState: true,
            preserveScroll: true,
          }
        )
      }, 150),
      deep: true,
    },
  },

  methods: {
    reset() {
      this.filterForm = mapValues(this.filterForm, () => null)
    },
  },
}
</script>
