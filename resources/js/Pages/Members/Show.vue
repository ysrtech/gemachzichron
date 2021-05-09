<template>
  <div>
    <member-tabs :member="member">
      <div class="max-w-7xl mx-auto grid grid-cols-3 gap-10">

        <!-- Left side -->
        <div class="col-span-3 md:col-span-2 space-y-10">
          <member-details-section :member="member"/>
          <!--      <membership-section :member="member"/>-->
          <!--      <subscriptions-section-->
          <!--        v-if="member.membership"-->
          <!--        :subscriptions="member.membership.subscriptions"-->
          <!--        :membership-id="member.membership.id"-->
          <!--      />-->
          <!--      <loans-section-->
          <!--        v-if="member.membership"-->
          <!--        :member="member"-->
          <!--      />-->
        </div>

        <!-- Right side -->
        <div class="col-span-3 md:col-span-1 space-y-10">
          <!--      <dependents-section :member="member" />-->
          <!--      <given-endorsements-section :endorsements="member.given_endorsements" />-->
        </div>

      </div>
    </member-tabs>
    <teleport v-if="isMounted" to="#header">
      {{ member.first_name + ' ' + member.last_name }}
      <app-badge v-if="member.deleted_at" color="red">Archived Member</app-badge>
    </teleport>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import MemberTabs from "@/Pages/Members/Tabs";
import MemberDetailsSection from "./Sections/MemberDetailsSection";
import AppBadge from "@/Components/UI/Badge";
import IsMounted from "@/Mixins/IsMounted";
// import DependentsSection from "./Sections/DependentsSection";
// import GivenEndorsementsSection from "./Sections/GivenEndorsementsSection";
// import MembershipSection from "./Sections/MembershipSection";
// import SubscriptionsSection from "./Sections/SubscriptionsSection";
// import LoansSection from "./Sections/LoansSection";

export default {

  layout: AppLayout,

  components: {
    MemberTabs,
    MemberDetailsSection,
    AppBadge
    // LoansSection,
    // SubscriptionsSection,
    // MembershipSection,
    // GivenEndorsementsSection,
    // DependentsSection,
  },

  mixins: [IsMounted],

  props: {
    member: Object,
  },
}
</script>
