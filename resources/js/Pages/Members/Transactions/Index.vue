<template>
  <div>
    <member-base :member="member">
      <div class="max-w-6xl mx-auto">
        <app-panel title="Transactions">
          <template #actions>
            <a
              v-if="member.transactions.length"
              :href="$route('members.transactionsreport', {member: member.id})"
              download>
                <app-button>Download Report</app-button>
            </a>
            
            </template>
          <template #content>
            <transactions-table :transactions="member.transactions" :member="member"/>
            <div v-if="member.transactions.length === 0" class="px-6 py-10 text-center text-gray-500">No Transactions Found.</div>
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

export default {
  layout: AppLayout,

  components: {
    AppPanel,
    TransactionsTable,
    MemberBase
  },

  props: {
    member: Object,
  },
}
</script>
