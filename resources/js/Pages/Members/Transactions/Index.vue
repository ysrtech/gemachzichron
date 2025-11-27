<template>
  <div>
    <member-base :member="member">
      <div class="max-w-12xl mx-auto">
        <app-panel title="Transactions">
          <template #actions>
            <a
              v-if="transactions.data.length"
              :href="$route('members.transactionsreport', {member: member.id})"
              download>
                <app-button>Download Report</app-button>
            </a>
            
            </template>
          <template #content>
            <transactions-table :transactions="transactions.data" :member="member"/>
            <div v-if="transactions.data.length === 0" class="px-6 py-10 text-center text-gray-500">No Transactions Found.</div>
            
            <!-- Pagination -->
            <div v-if="transactions.data.length" class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
              <pagination :response="transactions"/>
            </div>
          </template>
        </app-panel>
      </div>
    </member-base>
  </div>
</template>

<script>
import MemberBase from "@/Pages/Members/MemberBase";
import AppLayout from "@/Layouts/AppLayout";
import AppPanel from "@/Components/Panel";
import TransactionsTable from "@/Components/App/Transactions/TransactionsTable"
import Pagination from "@/Components/Pagination";

export default {
  layout: AppLayout,

  components: {
    AppPanel,
    TransactionsTable,
    MemberBase,
    Pagination
  },

  props: {
    member: Object,
    transactions: Object,
  },
}
</script>
