<template>
  <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
    <td class="border-t">
      <inertia-link class="px-6 py-4 flex items-center focus:text-primary-500"
                    :href="route('members.show', member.id)">
        {{ member.last_name + ', ' + member.first_name }}
        <i v-if="member.deleted_at" class="material-icons flex-shrink-0 text-sm text-red-300 ml-2"
           title="Archived member">delete</i>
        <i v-if="true" class="material-icons flex-shrink-0 text-sm text-green-300 ml-2"
           title="Active Membership">verified_user</i>
      </inertia-link>
    </td>
    <td class="border-t">
      <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                    :href="route('members.show', member.id)" tabindex="-1">
        {{ member.total_paid }}
      </inertia-link>
    </td>
    <td class="border-t">
      <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                    :href="route('members.show', member.id)" tabindex="-1">
        {{ member.home_phone }}
      </inertia-link>
    </td>
    <td class="border-t">
      <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                    :href="route('members.show', member.id)" tabindex="-1">
        {{ member.mobile_phone }}
      </inertia-link>
    </td>
    <td class="border-t">
      <inertia-link class="px-6 py-4 flex items-center focus:outline-none"
                    :href="route('members.show', member.id)" tabindex="-1">
        {{ member.email }}
      </inertia-link>
    </td>
    <td class="border-t w-px p-0">
      <jet-dropdown align="right" width="36" class="mx-2">
        <template #trigger>
          <div class="px-2 flex items-center">
            <button class="material-icons focus:outline-none rounded-full p-1 hover:bg-gray-200 focus:bg-gray-300">more_vert</button>
          </div>
        </template>

        <template #content>
          <jet-dropdown-link @click.native="confirmingArchiveMember = true" as="button" v-if="!member.deleted_at">
            <div class="flex items-center">
              <i class="material-icons-outlined mr-3 text-gray-400">archive</i>
              <div>Archive</div>
            </div>
          </jet-dropdown-link>
          <jet-dropdown-link @click.native="restoreMember" as="button" v-else>
            <div class="flex items-center">
              <i class="material-icons-outlined mr-3 text-gray-400">unarchive</i>
              <div>Restore</div>
            </div>
          </jet-dropdown-link>
          <jet-dropdown-link href="#">
            <div class="flex items-center">
              <i class="material-icons-outlined mr-3 text-gray-400">content_copy</i>
              <div>Duplicate</div>
            </div>
          </jet-dropdown-link>
        </template>
      </jet-dropdown>
    </td>

    <!-- Archive Member Confirmation Modal -->
    <jet-dialog-modal :show="confirmingArchiveMember" @close="confirmingArchiveMember = false" max-width="sm">
      <template #title>
        Archive Member
      </template>

      <template #content>
        Are you sure you want to archive <strong>{{ member.first_name + ' ' + member.last_name}}</strong>?
      </template>

      <template #footer>
        <jet-secondary-button @click.native="confirmingArchiveMember = false">
          Cancel
        </jet-secondary-button>

        <jet-button class="ml-2" @click.native="deleteMember" :class="{ 'opacity-25': form.processing }" :processing="form.processing">
          Confirm
        </jet-button>
      </template>
    </jet-dialog-modal>
  </tr>
</template>

<script>
import JetDropdown from "../Dropdown";
import JetDropdownLink from "../DropdownLink";
import JetButton from "../Button";
import JetDialogModal from "../DialogModal";
import JetSecondaryButton from "../SecondaryButton";

export default {
  components: {
    JetDropdown,
    JetDropdownLink,
    JetButton,
    JetDialogModal,
    JetSecondaryButton,
  },

  props: {
    member: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      confirmingArchiveMember: false,
      form: this.$inertia.form()
    }
  },

  methods: {
    deleteMember() {
      this.form.delete(route('members.destroy', this.member.id), {
        preserveScroll: true
      }).then(response => {
        if (! this.form.hasErrors()) {
          this.confirmingArchiveMember = false;
        }
      })
    },

    restoreMember() {
      this.form.put(route('members.restore', this.member.id), {
        preserveScroll: true
      })
    }
  },
}
</script>
