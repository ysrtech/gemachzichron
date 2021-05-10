<template>
  <div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center px-1">
      <search-filter v-model="filterForm.search" class="w-full max-w-md mr-4" @reset="reset">
        <div class="p-4">

          <label class="block text-gray-700 text-xs">Membership type</label>
          <select
            v-model="filterForm.type" @change="reset('plan_type_id')"
            class="mt-1 w-full text-sm border focus:outline-none rounded p-1">
            <option :value="null">--</option>
            <option>Membership</option>
            <option>Pekudon</option>
          </select>

          <template v-if="filterForm.type !== 'Pekudon'">
            <label class="block text-gray-700 text-xs mt-2">Plan type</label>
            <select
              v-model="filterForm.plan_type_id"
              class="mt-1 w-full text-sm border focus:outline-none rounded p-1">
              <option :value="null">--</option>
              <option v-for="planType of planTypes" :key="planType.id" :value="planType.id">{{ planType.name }}</option>
            </select>
          </template>

          <label class="block text-gray-700 text-xs mt-2">Archived</label>
          <select
            v-model="filterForm.archived"
            class="mt-1 w-full text-sm border focus:outline-none rounded p-1">
            <option :value="null">Without Archived</option>
            <option value="with">With Archived</option>
            <option value="only">Only Archived</option>
          </select>

        </div>
      </search-filter>

    </div>

    <main class="flex-1 relative pb-8 z-0 overflow-y-auto mx-auto px-1">
      <div class="flex flex-col mt-2">
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
          <memberships-table :members="members"/>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {mapValues, pickBy, throttle} from "lodash";
import SearchFilter from "@/Components/App/SearchFilter";
import MembershipsTable from "./MembershipsTable";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Memberships'}, () => page),

  components: {
    MembershipsTable,
    SearchFilter
  },

  data() {
    return {
      planTypes: [],
      filterForm: {
        search: this.filters.search,
        type: this.filters.type,
        plan_type_id: this.filters.plan_type_id,
        archived: this.filters.archived,
      },
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
          this.$route('memberships.index'),
          Object.keys(query).length ? query : {},
          {
            preserveState: true,
          }
        )
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
    this.$axios.get(this.$route('plan-types.index'))
      .then(response => {
        this.planTypes = response.data.plan_types
      })
  }
}
</script>
