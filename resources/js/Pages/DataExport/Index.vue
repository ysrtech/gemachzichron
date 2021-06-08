<template>
  <div class="max-w-4xl mx-auto mt-3 grid grid-cols-1 gap-5 sm:grid-cols-3 lg:grid-cols-3">
    <a
      v-for="model in models"
      :href="$route('export.show', {model: model.name})"
      download
      class="bg-white hover:scale-105 p-4 rounded-md shadow transform duration-200">
      <span class="flex items-start space-x-5">
        <span class="material-icons-outlined text-2xl text-gray-400">{{ model.icon }}</span>
        <span class="flex flex-col space-y-2">
          <span class="font-medium text-gray-700">{{ model.title }}</span>
          <span class="text-gray-400 text-sm">{{ model.description }}</span>
        </span>
      </span>
    </a>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Data Export'}, () => page),

  data() {
    return {
      models: [
        {
          name: 'members',
          title: 'Members',
          icon: 'people',
          description: 'Download members contact details and membership status'
        },
        {
          name: 'dependents',
          title: 'Children',
          icon: 'escalator_warning',
          description: 'Download members children'
        },
        {
          name: 'subscriptions',
          title: 'Subscriptions',
          icon: 'subscriptions',
          description: 'Download members subscriptions'
        },
        {
          name: 'transactions',
          title: 'Transactions',
          icon: 'payments',
          description: 'Download members transactions'
        },
        {
          name: 'loans',
          title: 'Loans',
          icon: 'account_balance',
          description: 'Download members loans (download guarantors separately)'
        },
        {
          name: 'guarantors',
          title: 'Guarantors',
          icon: 'edit_note',
          description: 'Download loan guarantors'
        },
        {
          name: 'payment_methods',
          title: 'Payment Methods',
          icon: 'local_atm',
          description: 'Download members payment methods'
        }
      ]
    }
  }
}
</script>
