export default {
  methods: {
    /**
     * Force rendering partial (rendering from frontend)
     *
     * @param {string} component name of component to be rendered
     * @param {Object} props props of component to be rendered
     */
    renderPartial(component, props = {}) {
      this.$page.props.partial = {component, props}
    }
  }
}
