<template>
  <div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center px-1">
      <search-filter v-model="filterForm.search" class="w-full max-w-md mr-4" @reset="reset">
        <div class="p-4 space-y-4">

          <search-filter-select
            v-model="filterForm.type"
            @change="reset('plan_type_id')"
            label="Membership type"
            :options="{'--': null, Membership: 'Membership', Pekudon: 'Pekudon'}"
          />

          <search-filter-select
            v-if="filterForm.type !== 'Pekudon'"
            v-model="filterForm.plan_type_id"
            label="Plan type"
            :options="planTypes.reduce((option, type) => ({...option, [type.name]: type.id}) ,{'--': null})"
          />

          <search-filter-select
            v-model="filterForm.is_active"
            label="Active Membership"
            :options="{'--': null, 'Only Active': 'true', 'Only Inactive': 'false'}"
          />

          <search-filter-select
            v-model="filterForm.archived"
            label="Archived Member"
            :options="{'Without Archived': null, 'With Archived': 'with', 'Only Archived': 'only'}"
          />

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
import SearchFilterSelect from "@/Components/App/SearchFilterSelect";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Memberships'}, () => page),

  components: {
    SearchFilterSelect,
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
        is_active: this.filters.is_active,
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
