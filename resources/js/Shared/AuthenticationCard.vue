<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">

      <a href="/">
        <img src="/img/logo.png" alt="Logo" class="m-auto my-4 w-3/4">
      </a>

      <slot/>

    </div>
  </div>
</template>
