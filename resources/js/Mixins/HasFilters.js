import {mapValues, pickBy, throttle} from "lodash";

export default {

  data() {
    return {
      filterForm: {}, // to override
      route: window.location.href.split('?')[0]
    }
  },

  watch: {
    filterForm: {
      handler: throttle(function () {
        let query = pickBy(this.filterForm)
        this.$inertia.get(
          this.route,
          Object.keys(query).length ? query : {},
          {
            preserveState: true,
            preserveScroll: true,
            replace: true,
          }
        )
      }, 150),
      deep: true,
    },
  },

  computed: {
    appliedFiltersLength() {
      return Object.keys(pickBy(this.filterForm, (value, key) => value && key !== 'search')).length
    }
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
}
