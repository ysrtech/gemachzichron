<template>

  <app-dialog-modal :show="member" @close="$emit('close')" max-width="sm">
    <template #title>
      Archive Member
    </template>

    <template #content>
      Are you sure you want to archive <strong>{{ memberFullName }}</strong>?
    </template>

    <template #footer>
      <app-button color="secondary" @click.native="$emit('close')">
        Cancel
      </app-button>

      <app-button type="submit" color="danger" class="ml-2" @click.native="deleteMember" :processing="form.processing">
        Confirm
      </app-button>
    </template>
  </app-dialog-modal>

</template>

<script>
import AppButton from "../../../Shared/Button";
import AppDialogModal from "../../../Shared/DialogModal";

export default {
  name: "ArchiveConfirmation",

  data() {
    return {
      form: this.$inertia.form()
    }
  },

  components: {
    AppButton,
    AppDialogModal,
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
