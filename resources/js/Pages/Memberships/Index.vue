<template>
  <div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center px-1">
      <search-filter
        v-model="filterForm.search"
        :applied-filters-length="appliedFiltersLength"
        class="w-full max-w-md mr-4"
        @reset="reset">

        <search-filter-field
          v-model="filterForm.membership_type"
          @change="reset('plan_type_id')"
          type="select"
          label="Membership type"
          :options="Object.assign({'All': null}, MEMBERSHIP_TYPES)"
        />

        <search-filter-field
          v-if="filterForm.type !== MEMBERSHIP_TYPES.Pekudon"
          v-model="filterForm.plan_type_id"
          type="select"
          label="Plan type"
          :options="planTypes.reduce((option, type) => ({...option, [type.name]: type.id}) ,{'All': null})"
        />

        <search-filter-field
          v-model="filterForm.active_membership"
          type="select"
          label="Active Membership"
          :options="{'All': null, 'Only Active': 'true', 'Only Inactive': 'false'}"
        />

        <search-filter-field
          v-model="filterForm.archived"
          type="select"
          label="Archived Member"
          :options="{'Without Archived': null, 'With Archived': 'with', 'Only Archived': 'only'}"
        />
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
import SearchFilter from "@/Components/App/SearchFilter";
import MembershipsTable from "./MembershipsTable";
import SearchFilterField from "@/Components/App/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";
import {MEMBERSHIP_TYPES} from "@/config/memberships";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Memberships'}, () => page),

  components: {
    SearchFilterField,
    MembershipsTable,
    SearchFilter
  },

  mixins: [HasFilters],

  data() {
    return {
      MEMBERSHIP_TYPES,
      planTypes: [],
      filterForm: {
        search: this.filters.search,
        membership_type: this.filters.membership_type,
        plan_type_id: this.filters.plan_type_id,
        archived: this.filters.archived,
        active_membership: this.filters.active_membership,
      },
    }
  },

  props: {
    members: Object,
    filters: Object,
  },

  created() {
    this.$axios.get(this.$route('plan-types.index'))
      .then(response => {
        this.planTypes = response.data.plan_types
      })
  }
}
</script>
