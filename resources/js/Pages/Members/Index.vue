<template>
  <div>

    <div class="px-6">

      <div class="mb-6 flex justify-between items-center">
        <search-filter v-model="filterForm.search" class="w-full max-w-md mr-4" @reset="reset">
          <div class="p-5">
            <label class="block text-gray-700 text-sm">Archived:</label>
              <select v-model="filterForm.archived" class="mt-1 w-full form-select text-sm">
                <option :value="null">Without Archived</option>
                <option value="with">With Archived</option>
                <option value="only">Only Archived</option>
              </select>

              <!-- Todo: Other filters here -->
          </div>
        </search-filter>

        <app-button @click.native="showCreateMemberModal = true">
          <span>Create</span>
          <span class="hidden md:inline pl-1"> Member</span>
        </app-button>

      </div>

      <members-table :members="members.data"></members-table>
      <pagination :links="members.links"/>
    </div>

    <member-form-modal :show="showCreateMemberModal" @close="showCreateMemberModal = false" />

  </div>
</template>

<script>
import AppLayout from '../../Layouts/AppLayout'
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";
import Pagination from "../../Shared/Pagination";
import SearchFilter from "./Components/SearchFilter";
import AppButton from "../../Shared/Button"
import MemberFormModal from "./Components/MemberFormModal";
import MembersTable from "./Components/MembersTable";

export default {
  layout: AppLayout,
  header: 'Members',
  components: {
    MembersTable,
    MemberFormModal,
    AppLayout,
    Pagination,
    SearchFilter,
    AppButton,
  },
  props: {
    members: Object,
    filters: Object,
  },
  data() {
    return {
      showCreateMemberModal: false,
      filterForm: {
        search: this.filters.search,
        archived: this.filters.archived,
      },
    }
  },
  watch: {
    filterForm: {
      handler: throttle(function () {
        let query = pickBy(this.filterForm)
        this.$inertia.replace(this.route('members.index', Object.keys(query).length ? query : {}))
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
