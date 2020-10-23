<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
        Members
      </h2>
    </template>

    <div class="px-6">

      <div class="mb-6 flex justify-between items-center">
        <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
          <div class="p-6">
            <label class="block text-gray-700">Archived:</label>
              <select v-model="form.archived" class="mt-1 w-full form-select">
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
          <tr v-for="(member, index) in members.data" :key="member.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:text-primary-500"
                            :href="route('members.show', member.id)">
                {{ member.last_name + ', ' + member.first_name }}
                <i v-if="true" class="material-icons flex-shrink-0 text-sm text-green-300 ml-2"
                   title="Active Membership">verified_user</i>
                <i v-if="member.deleted_at" class="material-icons flex-shrink-0 text-red-200 ml-2"
                   title="Archived member">delete</i>
              </inertia-link>
            </td>
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                            :href="route('members.show', member.id)" tabindex="-1">
                {{ member.total_paid }}
              </inertia-link>
            </td>
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                            :href="route('members.show', member.id)" tabindex="-1">
                {{ member.home_phone }}
              </inertia-link>
            </td>
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                            :href="route('members.show', member.id)" tabindex="-1">
                {{ member.mobile_phone }}
              </inertia-link>
            </td>
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                            :href="route('members.show', member.id)" tabindex="-1">
                {{ member.email }}
              </inertia-link>
            </td>
            <td class="border-t w-px p-0">
              <jet-dropdown align="right" width="36" class="mx-2">
                <template #trigger>
                  <div class="px-2 flex items-center">
                    <button class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">more_vert</button>
                  </div>
                </template>

                <template #content>
                  <jet-dropdown-link href="#">
                    <div class="flex items-center">
                      <i class="material-icons-outlined mr-3 text-gray-400">archive</i>
                      <div>Archive</div>
                    </div>
                  </jet-dropdown-link>
                  <jet-dropdown-link href="#">
                    <div class="flex items-center">
                      <i class="material-icons-outlined mr-3 text-gray-400">content_copy</i>
                      <div>Duplicate</div>
                    </div>
                  </jet-dropdown-link>
                </template>
              </jet-dropdown>
            </td>
          </tr>
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

export default {
  components: {
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
      form: {
        search: this.filters.search,
        archived: this.filters.archived,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('members.index', Object.keys(query).length ? query : {remember: 'forget'}))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
