<template>
  <div>

    <div class="flex items-center mb-4 w-full">
      <h2 class="font-medium text-3xl">{{ member.first_name + ' ' + member.last_name }}</h2>


      <div class="flex text-orange-600 ml-5" v-if="member.deleted_at">
        <div class="material-icons-outlined">report_problem</div>
        <div class="ml-2">Archived member</div>

        <inertia-link
          method="put" :href="$route('members.restore', member.id)"
          class="font-medium hover:text-orange-700 underline ml-2">
          Restore
        </inertia-link>
      </div>

      <div class="ml-auto text-right" v-else>
        <button
          type="button" class="text-orange-600 hover:text-orange-700 focus:outline-none"
          @click="confirmArchive = member">
          Archive Member
        </button>
      </div>
    </div>

    <div class="grid grid-cols-6 gap-6">

      <div class="col-span-6 sm:col-span-4">
        <member-info-card :member="member"/>

        <membership-card
          :membership="member.membership"
          @create-membership="createMembership = true"
          class="mt-6"
        />

        <subscriptions-card
          v-if="member.membership"
          :subscriptions="member.membership.subscriptions"
          @create-subscription="createSubscription = true"
          class="mt-6"
        />

        <loans-card
          v-if="member.membership"
          :dependents="member.dependents"
          :membership-id="member.membership.id"
          :loans="member.membership.loans"
          class="mt-6"
        />

      </div>

      <div class="col-span-6 sm:col-span-2">
        <dependents-card :dependents="member.dependents" :member="member"/>
        <given-endorsements-card :givenEndorsements="member.given_endorsements" class="mt-6"/>
      </div>

    </div>

    <archive-confirmation
      :member="confirmArchive"
      @close="confirmArchive = null"
    />

    <membership-form-modal
      :show="createMembership"
      :member="member"
      @close="createMembership = false"
    />

    <subscription-form-modal
      :show="createSubscription"
      :membership="member.membership"
      @close="createSubscription = false"
    />

  </div>
</template>

<script>
import AppLayout from "../../Layouts/AppLayout";
import MemberInfoCard from "./Components/MemberInfoCard";
import DependentsCard from "./Components/DependentsCard";
import ArchiveConfirmation from "./Components/ArchiveConfirmation";
import MembershipCard from "./Components/MembershipCard";
import SubscriptionsCard from "./Components/SubscriptionsCard";
import MembershipFormModal from "./Components/MembershipFormModal";
import SubscriptionFormModal from "./Components/SubscriptionFormModal";
import GivenEndorsementsCard from "./Components/GivenEndorsementsCard";
import LoansCard from "./Components/LoansCard";
import LoanFormModal from "./Components/LoanFormModal";

export default {
  name: "Show",
  layout: AppLayout,
  header: 'Members',

  data() {
    return {
      confirmArchive: null,
      createMembership: false,
      createSubscription: false,
    }
  },

  components: {
    LoanFormModal,
    LoansCard,
    GivenEndorsementsCard,
    SubscriptionFormModal,
    MembershipFormModal,
    SubscriptionsCard,
    MembershipCard,
    ArchiveConfirmation,
    DependentsCard,
    MemberInfoCard,
    AppLayout,
  },

  props: {
    member: Object
  }
}
</script>
