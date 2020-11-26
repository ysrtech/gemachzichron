<template>

  <div>

    <div class="mb-6 flex items-center">
      <search-filter v-model="filterForm.search" class="w-full max-w-md mr-4" @reset="reset">

        <div class="px-5 py-2">
          <label class="block text-gray-700 text-sm">Membership Type:</label>
          <select v-model="filterForm.type" @change="reset('plan_type_id')" class="mt-1 w-full form-select text-sm">
            <option :value="null">--</option>
            <option>Membership</option>
            <option>Pekudon</option>
          </select>
        </div>

        <div class="px-5 py-2" v-if="filterForm.type != 2">
          <label class="block text-gray-700 text-sm">Plan Types:</label>
          <select v-model="filterForm.plan_type_id" class="mt-1 w-full form-select text-sm">
            <option :value="null">--</option>
            <option v-for="planType of planTypes" :key="planType.id" :value="planType.id">{{ planType.name }}</option>
          </select>
        </div>

        <div class="px-5 py-2">
          <label class="block text-gray-700 text-sm">Archived:</label>
          <select v-model="filterForm.archived" class="mt-1 w-full form-select text-sm">
            <option :value="null">--</option>
            <option value="with">With Archived</option>
            <option value="only">Only Archived</option>
          </select>
        </div>

      </search-filter>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left text-xs text-gray-400 uppercase">
          <th class="px-6 pt-6 pb-4 font-medium">Name</th>
          <th class="px-6 pt-6 pb-4 font-medium">Membership Since</th>
          <th class="px-6 pt-6 pb-4 font-medium">Membership Type</th>
          <th class="px-6 pt-6 pb-4 font-medium">Plan type</th>
          <th class="px-6 pt-6 pb-4 font-medium">Total Paid</th>
        </tr>
        <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-for="member in members.data" :key="member.id">
          <td class="border-t">
            <inertia-link
              class="px-6 py-3 flex items-center focus:text-primary-500"
              :href="route('members.show', member.id)">
              {{ member.last_name + ', ' + member.first_name }}
              <i v-if="member.deleted_at"
                 class="material-icons flex-shrink-0 text-sm text-red-300 ml-2"
                 title="Archived member">delete</i>
            </inertia-link>
          </td>

          <td class="border-t">
            <inertia-link
              class="px-6 py-3 flex items-center focus:outline-none"
              :href="route('members.show', member.id)" tabindex="-1">
              {{ formatDate(member.membership.created_at) }}
            </inertia-link>
          </td>

          <td class="border-t">
            <inertia-link
              class="px-6 py-3 flex items-center focus:outline-none"
              :href="route('members.show', member.id)" tabindex="-1">
              {{ member.membership.type }}
            </inertia-link>
          </td>

          <td class="border-t">
            <inertia-link
              class="px-6 py-3 flex items-center focus:outline-none"
              :href="route('members.show', member.id)" tabindex="-1">
              {{ member.membership.plan_type ? member.membership.plan_type.name : null }}
            </inertia-link>
          </td>

          <td class="border-t">
            <inertia-link
              class="px-6 py-3 flex items-center focus:outline-none"
              :href="route('members.show', member.id)" tabindex="-1">
              {{ member.membership.total_paid }}
            </inertia-link>
          </td>
        </tr>

        <tr v-if="members.length === 0">
          <td class="border-t px-6 py-3" colspan="5">No memberships found.</td>
        </tr>

      </table>

    </div>

    <pagination :links="members.links"/>
  </div>
</template>

<script>
import AppLayout from '../../Layouts/AppLayout'
import Pagination from "../../Shared/Pagination";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";
import SearchFilter from "../Members/Components/SearchFilter";

export default {
  layout: AppLayout,
  header: 'Memberships',
  components: {
    SearchFilter,
    AppLayout,
    Pagination,
  },
  props: {
    members: Object,
    filters: Object,
  },
  data() {
    return {
      planTypes: [],
      filterForm: {
        search: this.filters.search,
        archived: this.filters.archived,
        type: this.filters.type,
        plan_type_id: this.filters.plan_type_id,
      },
    }
  },
  watch: {
    filterForm: {
      handler: throttle(function () {
        let query = pickBy(this.filterForm)
        this.$inertia.replace(this.route('memberships.index', Object.keys(query).length ? query : {}))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset(field = null) {
      if (field) {
        this.filterForm[field] = null
      } else {
        this.filterForm = mapValues(this.filterForm, () => null)
      }
    },
  },

  created() {
    axios.get(route('plan-types.index').url())
      .then(response => {
        this.planTypes = response.data.plan_types
      })
  }
}
</script>
