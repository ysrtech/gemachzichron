<template>
  <div class="w-full">

    <div class="flex space-x-1 items-center">
      <h3 class="text-xl font-medium leading-6 text-gray-900 mx-1 py-1.5">Member Details</h3>
      <inertia-link
        :href="$route('members.edit', member.id)"
        title="Edit Member details"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
        edit
      </inertia-link>
      <button
        v-if="!member.deleted_at"
        title="Archive Member"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
        @click="$inertia.delete($route('members.destroy', member.id))">
        delete
      </button>
      <button
        v-else
        title="Restore Member"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300"
        @click="$inertia.put($route('members.restore', member.id))">
        unarchive
      </button>
    </div>

    <div class="bg-white rounded-lg shadow divide-y divide-gray-200 w-full mt-2 mx-1">
      <div class="px-4 py-5 sm:px-6">
        <dl class="grid grid-cols-2 gap-x-4 gap-y-8">
          <div>
            <dt class="text-xs font-medium text-gray-400">First Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.first_name }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Last Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.last_name }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Hebrew Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.hebrew_name }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Wife's Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.wife_name }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Email</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.email }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Home Phone</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.home_phone }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Cellphone</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.mobile_phone }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-400">Shtibel</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ member.shtibel }}</dd>
          </div>
        </dl>
      </div>
    </div>

  </div>
</template>

<script>
export default {

  props: {
    member: Object,
  },
}
</script>
