<template>
  <main class="overflow-y-auto max-w-12xl mx-auto">
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
<div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="py-5 px-8 flex-1">
          <div class="flex-col items-center">
            <div class="shrink-0">
              <h2 class="font-medium text-5xl">{{ loans_count }}</h2>
            </div>
            <div class="w-0 flex-1">
              <span class="text-xl text-gray-500">Loans Given</span>
            </div>
          </div>
        </div>
        <inertia-link
          :href="$route('loans.index')"
          class="py-3 px-8 bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-900 text-sm font-medium hover:underline">
          View Loans
        </inertia-link>
      </div>
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="py-5 px-8 flex-1">
          <div class="flex-col items-center">
            <div class="shrink-0">
              <h2 class="font-medium text-5xl"><money class="text-4xl" :amount="total_loans"/></h2>
            </div>
            <div class="w-0 flex-1">
              <span class="text-xl text-gray-500">
                Total In Loans Given
              </span>
            </div>
          </div>
        </div>
        <inertia-link
          :href="$route('loans.index')"
          class="py-3 px-8 bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-900 text-sm font-medium hover:underline">
          View Loans
        </inertia-link>
      </div>
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
          <div class="shrink-0 flex-1">
              <h2 class="font-medium text-5xl"><money class="text-4xl" :amount="total_loans_outstanding"/></h2>
            </div>
          <div class="bg-gray-50 py-3 px-8">
            <span class="text-gray-700 font-medium text-sm">
              Total Loans Outstanding
            </span>
          </div>
        </div>

         <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="py-5 px-8 flex-1">
          <div class="flex-col items-center">
            <div class="shrink-0">
              <h2 class="font-medium text-5xl"><money class="text-4xl" :amount="total_capital"/></h2>
            </div>
            <div class="w-0 flex-1">
              <span class="text-xl text-gray-500">
                Total Capital
              </span>
            </div>
          </div>
        </div>
        <inertia-link
          :href="$route('loans.index')"
          class="py-3 px-8 bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-900 text-sm font-medium hover:underline">
          View Loans
        </inertia-link>
      </div>
       <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="py-5 px-8 flex-1">
          <div class="flex-col items-center">
            <div class="shrink-0">
              <h2 class="font-medium text-5xl"><money class="text-4xl" :amount="total_membership_fees"/></h2>
            </div>
            <div class="w-0 flex-1">
              <span class="text-xl text-gray-500">
                Total Membership & Decline Fees
              </span>
            </div>
          </div>
        </div>
        <inertia-link
          :href="$route('loans.index')"
          class="py-3 px-8 bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-900 text-sm font-medium hover:underline">
          View Loans
        </inertia-link>
      </div>
      <!-- stats -->
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="py-5 px-8 flex-1">
          <div class="flex items-center">
            <div class="shrink-0">
              <h2 class="font-medium text-5xl">{{ pending_manual_count }}</h2>
            </div>
            <div class="ml-5 w-0 flex-1">
              <span class="text-xl text-gray-500">
                Pending Manual Transactions
              </span>
            </div>
          </div>
        </div>
        <inertia-link
          :href="$route('transactions.index', {
            status: TRANSACTION_STATUSES.Pending, gateway: AVAILABLE_GATEWAYS.Manual
          })"
          class="py-3 px-8 bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-900 text-sm font-medium hover:underline">
          View
        </inertia-link>
      </div>
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="py-5 px-8 flex-1">
          <div class="flex items-center">
            <div class="shrink-0">
              <h2 class="font-medium text-5xl">{{ failed_count }}</h2>
            </div>
            <div class="ml-5 w-0 flex-1">
              <span class="text-xl text-gray-500">
                Failed Transactions
              </span>
            </div>
          </div>
        </div>
        <inertia-link
          :href="$route('transactions.index', {status: TRANSACTION_STATUSES.Fail})"
          class="py-3 px-8 bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-900 text-sm font-medium hover:underline">
          View
        </inertia-link>
      </div>
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
          <div class="py-5 px-8 flex-1">
            <money class="text-4xl" :amount="month_success_total"/>
          </div>
          <div class="bg-gray-50 py-3 px-8">
            <span class="text-gray-700 font-medium text-sm">
              Successful Transactions Total &dash; {{ currentDate({year: 'numeric', month: 'long'}) }}
            </span>
          </div>
        </div>


      <app-panel class="col-span-1 sm:col-span-2 lg:col-span-3 mt-8 sm:overflow-auto" title="Recent Transactions">
        <template #content>
          <transactions-table :transactions="recent_transactions"/>
          <div class="text-gray-700 text-center font-medium py-3">
            <inertia-link class="hover:underline" :href="$route('transactions.index')">View All</inertia-link>
          </div>
        </template>
      </app-panel>
    </div>
  </main>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import {TRANSACTION_STATUSES} from '@/config/transactions';
import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import {currentDate} from "@/helpers/dates";
import Money from "@/Components/Money";
import TransactionsTable from "@/Components/App/Transactions/TransactionsTable";
import AppPanel from "@/Components/Panel";

export default {
  layout: (h, page) => h(AppLayout, {title: 'Dashboard'}, () => page),

  components: {
    AppPanel,
    TransactionsTable,
    Money
  },

  props: {
    recent_transactions: Array,
    pending_manual_count: Number,
    failed_count: Number,
    loans_count: Number,
    month_success_total: [Number, String],
    total_loans: [Number, String],
    total_loans_outstanding: [Number, String],
    total_membership_fees: [Number, String],
    total_capital: [Number, String]

  },

  data() {
    return {
      TRANSACTION_STATUSES,
      AVAILABLE_GATEWAYS,
    }
  },

  methods: {
    currentDate
  }
}
</script>
