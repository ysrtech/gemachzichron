<template>
  <div>
    <div>Data Export</div>
    Members Columns <pre>{{members}}</pre>
    Children Columns <pre>{{children}}</pre>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  layout: (h, page) => h(AppLayout, {header: 'Data Export'}, () => page),

  props: {
    members: Array,
    children: Array
  }
}
</script>
