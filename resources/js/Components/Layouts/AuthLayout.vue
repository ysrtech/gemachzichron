<template>
  <base-layout :login-modal="false">

    <div class="min-h-screen flex flex-col justify-center items-center pt-0 bg-gray-100">
      <div class="w-full sm:max-w-md px-6 py-4 sm:bg-white sm:shadow-md overflow-hidden sm:rounded-lg">

        <app-logo class="h-24 mx-auto my-5"/>

        <slot/>

      </div>
    </div>
  </base-layout>
</template>
<script>
import AppLogo from "@/Components/UI/Logo";
import BaseLayout from "@/Components/Layouts/BaseLayout";

export default {
  components: {
    BaseLayout,
    AppLogo,
  }
}
</script>
