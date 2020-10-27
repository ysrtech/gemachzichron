<template>

  <jet-dialog-modal :show="member" @close="$emit('close')" max-width="sm">
    <template #title>
      Archive Member
    </template>

    <template #content>
      Are you sure you want to archive <strong>{{ memberFullName }}</strong>?
    </template>

    <template #footer>
      <jet-button color="secondary" @click.native="$emit('close')">
        Cancel
      </jet-button>

      <jet-button type="submit" color="danger" class="ml-2" @click.native="deleteMember" :processing="form.processing">
        Confirm
      </jet-button>
    </template>
  </jet-dialog-modal>

</template>

<script>
import JetButton from "../../../Shared/Button";
import JetDialogModal from "../../../Shared/DialogModal";

export default {
  name: "ArchiveConfirmation",

  data() {
    return {
      form: this.$inertia.form()
    }
  },

  components: {
    JetButton,
    JetDialogModal,
  },

  props: {
    member: Object
  },

  computed: {
    memberFullName() {
      return this.member?.first_name + ' ' + this.member?.last_name
    }
  },

  methods: {
    deleteMember() {
      this.form.delete(route('members.destroy', this.member.id), {
        preserveScroll: true,
        onSuccess: () => {
          if (!this.form.hasErrors()) {
            this.$emit('close');
          }
        }
      })
    }
  }

}
</script>
