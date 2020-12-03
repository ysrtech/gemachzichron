<template>
  <div>
    <div class="grid grid-cols-3 gap-6">

      <div class="col-span-1 border rounded-md py-2 px-5 bg-white">
        <div class="text-xl font-bold my-2">General</div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">ID</div>
          <div class="font-medium text-lg">{{ invoice.id }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Date</div>
          <div class="font-medium text-lg">{{ formatDate(invoice.created_at) }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Subscription ID</div>
          <div class="font-medium text-lg">{{ invoice.subscription_id }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Amount</div>
          <div class="font-medium text-lg">${{ invoice.amount }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Gemach Fee Amount</div>
          <div class="font-medium text-lg">${{ invoice.gemach_fee }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Credit Card Processing Amount</div>
          <div class="font-medium text-lg">{{ invoice.cc_processing_fee ? '$' + invoice.cc_processing_fee.toFixed(2) : 'N/A' }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Total Amount</div>
          <div class="font-medium text-lg">${{ invoice.total }}</div>
        </div>
      </div>

      <div class="col-span-1 border rounded-md py-2 px-5 bg-white">
        <div class="text-xl font-bold my-2">Transactions</div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Status</div>
          <div class="font-medium text-lg" :class="invoice.status ? 'text-green-500' : 'text-red-500'">{{ invoice.status ? 'PAID' : 'PENDING' }}</div>
        </div>
        <div class="mt-4" v-if="invoice.status">
          <div class="text-sm text-gray-400 font-medium">Payment Date</div>
          <div class="font-medium text-lg">{{ formatDate(invoice.payment_date) }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-400 font-medium">Payment method</div>
          <div class="font-medium text-lg" v-if="invoice.payment_method">
            {{ invoice.payment_method.type }}
          </div>
          <div class="font-medium text-xs text-gray-500" v-if="invoice.payment_method">
            {{ '****' + invoice.payment_method.last_four_digits }}
          </div>
        </div>

        <pre>
          {{ invoice.transactions }}
        </pre>
      </div>

      <div class="col-span-1 border rounded-md py-2 px-5 bg-white">
        <div class="text-xl font-bold my-2">Member</div>
        <pre>
          {{ invoice.subscription.membership.member }}
        </pre>
      </div>

    </div>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  layout: AppLayout,
  header: 'Invoice',
  props: {
    invoice: Object
  },

  computed: {
    paymentMethod() {
      let type = this.invoice?.payment_method?.type;

      if (type === 'Credit Card') {
        type += `   ***${this.invoice.payment_method.last_four_digits}  exp ${this.subscription.payment_method.cc_expiration}`
      }

      return type
    }
  }
}
</script>
