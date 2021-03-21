<template>
  <div class="max-w-7xl mx-auto grid grid-cols-3 gap-10">

      <subscriptions-section
        v-if="member.membership"
        :subscriptions="member.membership.subscriptions"
        :membership-id="member.membership.id"
      />
    </div>

</template>
