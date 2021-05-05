<template>
  <site-nav/>
  <slot/>
  <partials/>
</template>

<script>
import Partials from "@/Partials/partials";
import SiteNav from "@/Components/App/SiteNav";

export default {
  components: {
    SiteNav,
    Partials,
  }
}
</script>
