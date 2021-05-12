export default {
  data() {
    return {
      isMounted: false
    }
  },

  mounted() {
    this.isMounted = true
  },

  beforeUnmount() {
    this.isMounted = false
  }
}
