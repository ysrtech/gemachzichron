<template>
  <div class="bg-white shadow rounded-md px-6">

    <div class="py-4">
      <div class="text-xl font-medium">Given Endorsements</div>
    </div>

    <div class="pb-4">
      <div class="bg-white rounded border border-gray-200 overflow-x-auto text-sm">

        <div v-for="(endorsement, index) in givenEndorsements" :key="index" :class="{'border-t': index !== 0}">
          <inertia-link
            class="px-4 py-3 flex items-center hover:bg-gray-100"
            :href="route('members.show', endorsement.membership.member.id).url()">
            {{ endorsement.membership.member.first_name + ' ' + endorsement.membership.member.last_name }}
          </inertia-link>
        </div>

        <div v-if="givenEndorsements.length === 0" class="px-6 py-4">
          No given endorsements.
        </div>

      </div>
    </div>

  </div>
</template>

<script>

export default {

  props: {
    givenEndorsements: Array,
  },
}
</script>
