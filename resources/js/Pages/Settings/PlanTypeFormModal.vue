<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">
    <div class="px-6 py-4 text-xl font-medium">
      {{ planType ? 'Edit Plan Type' : 'Create Plan Type' }}
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">
        <app-input
          v-model="form.name"
          :error="form.errors.name"
          label="Name"
          type="text"
          @update:model-value="form.clearErrors('name')"
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
          {{ planType ? 'Update' : 'Create' }}
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
    }
  },

  props: {
    show: Boolean,
    planType: Object,
  },

  emits: ['close'],

  watch: {
    show(val) {
      if (val) {
        this.form = this.$inertia.form({
          name: this.planType?.name || '',
        });
      }
    }
  },

  methods: {
    submit() {
      if (this.planType) {
        this.form.put(this.$route('settings.plan-types.update', this.planType.id), {
          onSuccess: () => {
            this.$emit('close');
          }
        });
      } else {
        this.form.post(this.$route('settings.plan-types.store'), {
          onSuccess: () => {
            this.$emit('close');
          }
        });
      }
    }
  }
}
</script>
