<template>
  <div>

    <div class="mb-6 flex justify-between items-center">
      <search-filter class="w-full max-w-md mr-4" :filters="filterForm" @reset="reset" @status-select="updateStatus" @sort-select="updateSort" />
    </div>

    <invoices-table :invoices="invoices.data"></invoices-table>
    <pagination :links="invoices.links"/>

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
import InvoicesTable from "./Components/InvoicesTable";

export default {
  layout: AppLayout,
  header: 'Invoices',
  components: {
    InvoicesTable,
    AppLayout,
    Pagination,
    SearchFilter,
    AppButton,
  },
  props: {
    invoices: Object,
    filters: Object,
  },

  data() {
    return {
      filterForm: {
        sort: this.filters['sort'],
        status: this.filters.status,
        membership_id: this.filters.membership_id,
        subscription_id: this.filters.subscription_id,
      },
    }
  },

  watch: {
    filterForm: {
      handler: throttle(function () {
        let query = pickBy(this.filterForm)
        this.$inertia.replace(this.$route('invoices.index', Object.keys(query).length ? query : {}))
      }, 150),
      deep: true,
    },
  },

  methods: {
    updateStatus(event) {
      this.filterForm.status = event;
    },

    updateSort(event) {
      this.filterForm.sort = event;
    },

    reset() {
      this.filterForm = mapValues(this.filterForm, () => null)
    },
  },
}
</script>
