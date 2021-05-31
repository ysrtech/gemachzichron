<template>
  <pre>{{ transactions }}</pre>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {

  layout: (h, page) => h(AppLayout, {header: 'Transactions'}, () => page),

  props: {
    transactions: Object
  }
}
</script>
