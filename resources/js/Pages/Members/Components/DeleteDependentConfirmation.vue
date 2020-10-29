<template>
  <app-dialog-modal :show="dependent" @close="$emit('close')" max-width="sm">
    <template #title>
      Delete Dependent
    </template>

    <template #content>
      Are you sure you want to delete <strong>{{ dependentFullName }}</strong>?
    </template>

    <template #footer>
      <app-button color="secondary" @click.native="$emit('close')">
        Cancel
      </app-button>

      <app-button type="submit" color="danger" class="ml-2" @click.native="deleteDependent" :processing="form.processing">
        Confirm
      </app-button>
    </template>
  </app-dialog-modal>
</template>

<script>
import AppButton from "../../../Shared/Button";
import AppDialogModal from "../../../Shared/DialogModal";

export default {
  name: "DeleteDependentConfirmation",

  components: {
    AppButton,
    AppDialogModal,
  },

  props: {
    dependent: Object
  },

  data() {
    return {
      form: this.$inertia.form()
    }
  },

  computed: {
    dependentFullName() {
      return this.dependent?.first_name + ' ' + this.dependent?.last_name
    }
  },

  methods: {
    deleteDependent() {
      this.form.delete(route('dependents.destroy', this.dependent.id), {
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
