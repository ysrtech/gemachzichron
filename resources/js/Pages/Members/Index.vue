<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
        Members
      </h2>
    </template>

    <div class="px-6">

      <div class="mb-6 flex justify-between items-center">
        <search-filter v-model="filterForm.search" class="w-full max-w-md mr-4" @reset="reset">
          <div class="p-6">
            <label class="block text-gray-700">Archived:</label>
              <select v-model="filterForm.archived" class="mt-1 w-full form-select">
                <option :value="null">Without Archived</option>
                <option value="with">With Archived</option>
                <option value="only">Only Archived</option>
              </select>

              <!-- Todo: Other filters here -->
          </div>
        </search-filter>

        <jet-button type="button">
          <inertia-link :href="route('members.create')">
            <span>Create</span>
            <span class="hidden md:inline">Member</span>
          </inertia-link>
        </jet-button>

      </div>

      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
          <tr class="text-left font-bold">
            <th class="px-6 pt-6 pb-4">Name</th>
            <th class="px-6 pt-6 pb-4">Total Paid</th>
            <th class="px-6 pt-6 pb-4">Home Phone</th>
            <th class="px-6 pt-6 pb-4">Cellphone</th>
            <th class="px-6 pt-6 pb-4" colspan="2">Email</th>
          </tr>
          <index-row v-for="member in members.data" :key="member.id" :member="member"></index-row>
          <tr v-if="members.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No members found.</td>
          </tr>
        </table>
      </div>
      <pagination :links="members.links"/>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '../../Layouts/AppLayout'
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";
import Pagination from "../../Components/Pagination";
import SearchFilter from "../../Components/SearchFilter";
import JetDropdown from "../../Components/Dropdown"
import JetDropdownLink from "../../Components/DropdownLink"
import JetButton from "../../Components/Button"
import IndexRow from "../../Components/Members/IndexRow";

export default {
  components: {
    IndexRow,
    AppLayout,
    Pagination,
    SearchFilter,
    JetDropdown,
    JetDropdownLink,
    JetButton,
  },
  props: {
    members: Object,
    filters: Object,
  },
  data() {
    return {
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
        this.$inertia.replace(this.route('members.index', Object.keys(query).length ? query : {remember: 'forget'}))
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
