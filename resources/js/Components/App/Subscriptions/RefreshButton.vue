<template>
  <icon-button
    icon="sync"
    @click="refresh"
    v-tippy="{content: 'Sync with Gateway'}"
    :class="{'animate-spin': isRefreshing}"
    style="animation-direction: reverse"
  />
</template>

<script>
import IconButton from "@/Components/IconButton";
export default {
  components: {IconButton},
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
      this.$inertia.post(this.$route('subscription.refresh', this.subscriptionId), {}, {
        onFinish: () => this.isRefreshing = false,
        preserveScroll: true
      })
    }
  }
}
</script>
