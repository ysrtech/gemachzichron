<template>
  <div class="min-h-screen flex flex-col justify-center items-center pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md px-6 py-4 sm:bg-white sm:shadow-md overflow-hidden sm:rounded-lg">
      <inertia-link href="/">
        <app-logo class="h-24 mx-auto my-5"/>
      </inertia-link>
      <slot/>
    </div>
    <partials/>
  </div>
</template>

<script>
import AppLogo from "@/Components/UI/Logo";
import Partials from "@/Partials/partials";

export default {
  components: {
    Partials,
    AppLogo,
  }
}
</script>
