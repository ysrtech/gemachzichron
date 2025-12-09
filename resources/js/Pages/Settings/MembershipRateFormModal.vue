<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">
    <div class="px-6 py-4 text-xl font-medium">
      {{ isEdit ? 'Edit' : 'Add' }} Membership Rate for {{ planType?.name }}
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">
        <app-input
          v-model="form.rate"
          :error="form.errors.rate"
          label="Monthly Rate ($)"
          type="number"
          step="0.01"
          min="0"
          @update:model-value="form.clearErrors('rate')"
        />

        <app-input
          v-model="form.effective_date"
          :error="form.errors.effective_date"
          label="Effective Date"
          type="date"
          :disabled="isEdit"
          @update:model-value="form.clearErrors('effective_date')"
        />
      </div>

      <div class="px-6 py-4 flex justify-end space-x-2">
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
          Cancel
        </button>
        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 disabled:opacity-50">
          {{ isEdit ? 'Update' : 'Add' }} Rate
        </button>
      </div>
    </form>
  </modal>
</template>

<script>
import Modal from "@/Components/Modal";
import AppInput from "@/Components/FormControls/Input";

export default {
  components: {
    Modal,
    AppInput,
  },

  data() {
    return {
      form: this.$inertia.form(),
      isEdit: false,
    }
  },

  props: {
    show: Boolean,
    planType: Object,
    rate: [String, Number],
    date: String,
  },

  emits: ['close'],

  watch: {
    show(val) {
      if (val) {
        this.isEdit = this.rate !== null && this.date !== null;
        const today = new Date().toISOString().split('T')[0];
        this.form = this.$inertia.form({
          rate: this.rate || '',
          effective_date: this.date || today,
        });
      }
    }
  },

  methods: {
    submit() {
      if (this.isEdit) {
        // For edit, we need to delete old and create new since date is the key
        this.form.patch(this.$route('settings.plan-types.rate.update', this.planType.id), {
          onSuccess: () => {
            this.$emit('close');
          }
        });
      } else {
        this.form.patch(this.$route('settings.plan-types.rate.update', this.planType.id), {
          onSuccess: () => {
            this.$emit('close');
          }
        });
      }
    }
  }
}
</script>
