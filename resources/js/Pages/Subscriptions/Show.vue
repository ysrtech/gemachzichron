<template>
  <div>
    <pre>
      {{ subscription }}
    </pre>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  layout: AppLayout,
  props: {
    subscription: Object
  }
}
</script>
