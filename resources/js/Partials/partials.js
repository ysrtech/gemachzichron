import {defineAsyncComponent, ref, shallowRef} from "vue";

export default {

  template: `<component :is="partialComponent" v-if="partialComponent" v-bind="partialProps" @close="partialComponent = null; partialProps = {}"/>`,

  setup() {
    const partialComponent = shallowRef(null)
    const partialProps = ref({})

    const updatePartial = (partial) => {
      partialComponent.value = defineAsyncComponent(() => import(`@/Partials/${partial.component}`))
      partialProps.value = partial.props || {}
    }

    const removePartial = () => {
      partialComponent.value = null
      partialProps.value = {}
    }

    return {
      partialComponent,
      partialProps,
      updatePartial,
      removePartial
    }
  },

  watch: {
    /**
     * Changes can be triggered from backend response or from renderPartial method in RendersPartials mixin
     */
    '$page.props.partial': {
      immediate: true,
      handler(val) {
        if (val) {
          this.updatePartial(val)
        } else if (Object.keys(this.$page.props.errors).length > 0) {
          // do nothing (preserve partial)
        } else {
          this.removePartial()
        }
      }
    },
  },

  created() {
    // render the partial only - preserve current page, see PartialsServiceProvider (Inertia::partial(...))
    this.$inertia.on('invalid', (event) => {
      if (event.detail.response.data?.partial) {
        event.preventDefault()
        this.$page.props.partial = event.detail.response.data.partial
      }
    })
  },
}
