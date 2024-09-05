<template>
  <main class="overflow-y-auto max-w-12xl mx-auto">
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow p-5">
        <div class="text-xl text-gray-500">Loans</div>
        <h2 class="flex-1 font-medium text-4xl">{{ loans_count }}</h2>
      </div>

      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow p-5">
        <div class="text-xl text-gray-500">Loans Total</div>
        <h2 class="font-medium text-5xl flex-1">
          <money class="text-4xl" :amount="total_loans" :fraction="0"/>
        </h2>
      </div>

      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow p-5">
        <div class="text-xl text-gray-500">Outstanding Loans</div>
        <h2 class="font-medium text-5xl flex-1">
          <money class="text-4xl" :amount="total_loans_outstanding" :fraction="0"/>
        </h2>
      </div>

      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow p-5">
        <div class="text-xl text-gray-500">Gemach Capital</div>
        <h2 class="font-medium text-4xl">
          <money class="text-4xl" :amount="total_capital" :fraction="0"/>
        </h2>
      </div>

      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow p-5">
        <div class="text-xl text-gray-500">Membership & Decline Fees</div>
        <h2 class="font-medium text-4xl">
          <money class="text-4xl" :amount="total_membership_fees" :fraction="0"/>
        </h2>
      </div>

      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="flex flex-col p-5">
          <div class="text-xl text-gray-500">Pending Manual Transactions</div>
          <h2 class="font-medium text-4xl">{{ pending_manual_count }}</h2>
        </div>
        <inertia-link
          :href="
            $route('transactions.index', {
              status: TRANSACTION_STATUSES.Pending,
              gateway: AVAILABLE_GATEWAYS.Manual,
            })
          "
          class="
            py-3
            px-8
            bg-gray-50
            hover:bg-gray-100
            text-gray-700
            hover:text-gray-900
            text-sm
            font-medium
            hover:underline
          "
        >
          View
        </inertia-link>
      </div>
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow">
        <div class="flex flex-col p-5">
          <div class="text-xl text-gray-500">Failed Transactions</div>
          <h2 class="font-medium text-4xl">{{ failed_count }}</h2>
        </div>
        <inertia-link
          :href="
            $route('transactions.index', { status: TRANSACTION_STATUSES.Fail })
          "
          class="
            py-3
            px-8
            bg-gray-50
            hover:bg-gray-100
            text-gray-700
            hover:text-gray-900
            text-sm
            font-medium
            hover:underline
          "
        >
          View
        </inertia-link>
      </div>
      <div class="bg-white flex flex-col overflow-hidden rounded-lg shadow p-5">
        <div class="text-xl text-gray-500">Active Members</div>
        <h2 class="flex-1 font-medium text-4xl">{{ members_count }}</h2>
      </div>

      <app-panel
        class="col-span-1 sm:col-span-2 lg:col-span-4 mt-8 sm:overflow-auto"
        title="Recent Transactions"
      >
        <template #content>
          <transactions-table :transactions="recent_transactions" :showMember=1 />
          <div class="text-gray-700 text-center font-medium py-3">
            <inertia-link
              class="hover:underline"
              :href="$route('transactions.index')"
              >View All</inertia-link
            >
          </div>
        </template>
      </app-panel>
    </div>
  </main>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { TRANSACTION_STATUSES } from "@/config/transactions";
import { AVAILABLE_GATEWAYS } from "@/config/gateways";
import { currentDate } from "@/helpers/dates";
import Money from "@/Components/Money";
import TransactionsTable from "@/Components/App/Transactions/TransactionsTable";
import AppPanel from "@/Components/Panel";

export default {
  layout: (h, page) => h(AppLayout, { title: "Dashboard" }, () => page),

  components: {
    AppPanel,
    TransactionsTable,
    Money,
  },

  props: {
    recent_transactions: Array,
    pending_manual_count: Number,
    failed_count: Number,
    loans_count: Number,
    members_count: Number,
    month_success_total: [Number, String],
    total_loans: [Number, String],
    total_loans_outstanding: [Number, String],
    total_membership_fees: [Number, String],
    total_capital: [Number, String],
  },

  data() {
    return {
      TRANSACTION_STATUSES,
      AVAILABLE_GATEWAYS,
    };
  },

  methods: {
    currentDate,
  },
};
</script>
