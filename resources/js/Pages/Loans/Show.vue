<template>
  <div>
    <pre>{{ loan }}</pre>
    <teleport v-if="isMounted" to="#header">Loan #{{ loan.id }}</teleport>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import IsMounted from "@/Mixins/IsMounted";

export default {
  layout: AppLayout,

  mixins: [IsMounted],

  props: {
    loan: {
      type: Object,
      required: true
    }
  }
}
</script>
