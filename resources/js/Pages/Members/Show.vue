<template>
  <div class="max-w-7xl mx-auto grid grid-cols-3 gap-10">

    <!-- Left side -->
    <div class="col-span-3 md:col-span-2 space-y-10">
      <member-details-section :member="member"/>
      <membership-section :member="member"/>
      <subscriptions-section
        v-if="member.membership"
        :subscriptions="member.membership.subscriptions"
        :membership-id="member.membership.id"
      />
      <loans-section
        v-if="member.membership"
        :member="member"
      />
    </div>

    <!-- Right side -->
    <div class="col-span-3 md:col-span-1 space-y-10">
      <dependents-section :member="member" />
      <given-endorsements-section :endorsements="member.given_endorsements" />
    </div>

  </div>
</template>

<script>
import AppLayout from "@/Components/Layouts/AppLayout";
import MemberDetailsSection from "@/Pages/Members/Partials/MemberDetailsSection";
import DependentsSection from "@/Pages/Members/Partials/DependentsSection";
import GivenEndorsementsSection from "@/Pages/Members/Partials/GivenEndorsementsSection";
import MembershipSection from "@/Pages/Members/Partials/MembershipSection";
import SubscriptionsSection from "@/Pages/Members/Partials/SubscriptionsSection";
import LoansSection from "@/Pages/Members/Partials/LoansSection";

export default {

  layout: AppLayout,

  components: {
    LoansSection,
    SubscriptionsSection,
    MembershipSection,
    GivenEndorsementsSection,
    DependentsSection,
    MemberDetailsSection
  },

  mounted() {
    this.$page.header = `${this.member.first_name} ${this.member.last_name}`
  },

  props: {
    member: Object,
  },

  watch: {
    member() {
      this.$page.header = `${this.member.first_name} ${this.member.last_name}`
    }
  }
}
</script>
