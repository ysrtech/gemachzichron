<template>
  <div class="max-w-6xl mx-auto">
    <div class="mb-6 flex justify-between items-center px-1">
      <search-filter v-model="filterForm.search" class="w-full max-w-md mr-4" @reset="reset">
        <div class="p-4">

          <label class="block text-gray-700 text-xs">Status</label>
          <select v-model="filterForm.status" class="mt-1 w-full text-sm border focus:outline-none rounded p-1">
            <option :value="null">All</option>
            <option value="1">Paid</option>
            <option value="0">Pending</option>
          </select>

          <label class="block text-gray-700 text-xs mt-2">Sort</label>
          <select v-model="filterForm.sort" class="mt-1 w-full text-sm border focus:outline-none rounded p-1">
            <option value="-created_at">Invoice Date</option>
            <option value="-payment_date">Paid Date</option>
          </select>

        </div>
      </search-filter>

    </div>

    <main class="flex-1 relative pb-8 z-0 overflow-y-auto mx-auto px-1">
      <div class="flex flex-col mt-2">
        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
          <invoices-table :invoices="invoices"/>
        </div>
      </div>
    </main>

  </div>
</template>

<script>
import AppLayout from "@/Components/Layouts/AppLayout";
import InvoicesTable from "@/Pages/Invoices/Partials/InvoicesTable";
import {mapValues, pickBy, throttle} from "lodash";
import SearchFilter from "@/Components/App/SearchFilter";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Invoices'}, () => page),

  components: {
    InvoicesTable,
    SearchFilter
  },

  data() {
    return {
      filterForm: {
        search: this.filters.search,
        status: this.filters.status,
        sort: this.filters.sort,
      },
    }
  },

  props: {
    invoices: Object,
    filters: Object,
  },

  watch: {
    filterForm: {
      handler: throttle(function () {
        let query = pickBy(this.filterForm)
        this.$inertia.get(
          this.$route('invoices.index'),
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
    reset() {
      this.filterForm = mapValues(this.filterForm, () => null)
    },
  },
}
</script>
