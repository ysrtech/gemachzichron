<template>
  <button
    @click="refresh"
    v-tippy="{content: 'Refresh'}"
    :class="{'animate-spin': isRefreshing}"
    class="material-icons-outlined focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">
    refresh
  </button>
</template>

<script>
export default {

  props: {
    subscriptionId: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      isRefreshing: false
    }
  },

  methods: {
    refresh() {
      this.isRefreshing = true;
      this.$inertia.post(this.$route('subscriptions.refresh', this.subscriptionId), {}, {
        onFinish: () => this.isRefreshing = false,
        preserveScroll: true
      })
    }
  }
}
</script>
