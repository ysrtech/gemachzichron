<template>
  <div class="w-full">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Roles</h1>
    </div>
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
      <p class="text-gray-600">Roles management will go here. Define permissions and assign them to users.</p>
    </div>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  layout: (h, page) => h(AppLayout, { title: 'Roles' }, () => page),
};
</script>
