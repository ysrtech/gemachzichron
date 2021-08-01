<template>
  <div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center px-1">
      <search-filter
        v-model="filterForm.search"
        :applied-filters-length="appliedFiltersLength"
        class="w-full max-w-md mr-4"
        @reset="reset">

        <search-filter-field
          v-model="filterForm.membership_since"
          type="select"
          label="Membership"
          :options="{'All Members': '', 'Only With Membership': 'true', 'Only Without Membership': 'false'}"
        />

        <search-filter-field
          v-model="filterForm.archived"
          type="select"
          label="Archived"
          :options="{'Without Archived': null, 'With Archived': 'with', 'Only Archived': 'only'}"
        />
      </search-filter>

      <inertia-link :href="$route('members.create')">
        <app-button>New Member</app-button>
      </inertia-link>

    </div>

    <main class="flex-1 relative pb-8 z-0 overflow-y-auto mx-auto px-1">
      <div class="flex flex-col mt-2">
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
          <div>
            <table class="min-w-full divide-y divide-gray-200">

              <thead>
              <tr class="bg-gray-50 text-xs text-left text-gray-400 uppercase">
                <th v-for="title in ['Name', 'Home Phone', 'Cellphone', 'Email', '']" class="px-6 py-3 font-medium">{{ title }}</th>
              </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
              <tr
                @click="$inertia.get($route('members.show', member.id))"
                v-for="member in members.data"
                :key="member.id"
                class="bg-white text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer">
                <td class="px-6 py-3.5 whitespace-nowrap flex space-x-3 items-baseline">
                  <span>{{ member.last_name + ', ' + member.first_name }}</span>
                  <span
                    v-tippy="{ content: member.active_membership ? 'Active Membership' : 'No Active Membership' }"
                    class="block w-2 h-2 rounded-full"
                    :class="member.active_membership ? 'bg-green-500' : 'bg-red-500'">
                  </span>
                  <app-badge v-if="member.deleted_at" color="red" class="ml-1">Archived Member</app-badge>
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap">
                  {{ member.home_phone }}
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap">
                  {{ member.cell_phone }}
                </td>
                <td class="px-6 py-3.5 whitespace-nowrap">
                  {{ member.email }}
                </td>

                <td class="px-6 text-right w-px whitespace-nowrap text-sm text-gray-500 cursor-default" @click.stop>
                  <app-dropdown align="right" width="36">
                    <template #trigger>
                      <button class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
                        more_vert
                      </button>
                    </template>

                    <template #content>
                      <app-dropdown-link :href="$route('members.edit', member.id)">
                        <div class="flex items-center">
                          <i class="material-icons-outlined mr-3 text-gray-400 text-xl">edit</i>
                          <div>Edit</div>
                        </div>
                      </app-dropdown-link>
                      <app-dropdown-link as="button" href="#" @click.prevent="duplicateMember(member)">
                        <div class="flex items-center">
                          <i class="material-icons-outlined mr-3 text-gray-400 text-xl">content_copy</i>
                          <div>Duplicate</div>
                        </div>
                      </app-dropdown-link>
                      <app-dropdown-link as="button" :href="$route('members.destroy', member.id)" method="delete" v-if="!member.deleted_at">
                        <div class="flex items-center">
                          <i class="material-icons-outlined mr-3 text-gray-400 text-xl">archive</i>
                          <div>Archive</div>
                        </div>
                      </app-dropdown-link>
                      <app-dropdown-link as="button" :href="$route('members.restore', member.id)" method="put" v-else>
                        <div class="flex items-center">
                          <i class="material-icons-outlined mr-3 text-gray-400 text-xl">unarchive</i>
                          <div>Restore</div>
                        </div>
                      </app-dropdown-link>
                    </template>
                  </app-dropdown>
                </td>
              </tr>

              <tr v-if="members.total === 0">
                <td class="border-t px-6 py-10 text-center text-gray-500" colspan="6">No members found.</td>
              </tr>

              </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="members.total > members.per_page" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-300 sm:px-6">
              <app-pagination :response="members"/>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import SearchFilter from "@/Components/SearchFilter";
import AppPagination from "@/Components/Pagination";
import AppDropdownLink from "@/Components/DropdownLink";
import AppDropdown from "@/Components/Dropdown";
import SearchFilterField from "@/Components/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Members'}, () => page),

  components: {
    SearchFilterField,
    SearchFilter,
    AppPagination,
    AppDropdownLink,
    AppDropdown
  },

  data() {
    return {
      filterForm: {
        search: this.filters.search,
        archived: this.filters.archived,
        membership_since: this.filters.membership_since
      },
    }
  },

  mixins: [HasFilters],

  props: {
    members: Object,
    filters: Object,
  },

  methods: {
    duplicateMember(member) {
      const  duplicateValues = [
        'title', 'first_name', 'last_name', 'hebrew_first_name', 'hebrew_last_name',
        'wife_name', 'address', 'city', 'postal_code', 'email', 'home_phone',
        'cell_phone', 'wife_cell_phone', 'shtibel', 'father', 'father_in_law'
      ];

      const newMember = Object.keys(member)
        .filter(key => duplicateValues.includes(key))
        .reduce((obj, key) => {
          obj[key] = member[key];
          return obj;
        }, {});

      this.$inertia.post(this.$route('members.store'), newMember)
    }
  },
}
</script>
