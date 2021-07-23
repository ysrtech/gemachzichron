<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="planType">Edit Plan Type {{ planType.name }}</template>
      <template v-else>Create New Plan Type</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">
        <app-input
          id="name"
          v-model="form.name"
          :error="form.errors.name"
          label="Name"
          type="text"
          @update:model-value="form.clearErrors('name')"
        />
      </div>

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-end">
        <app-button color="secondary" @click="$emit('close')">Cancel</app-button>
        <app-button :processing="form.processing" class="ml-2" color="primary" type="submit">Submit</app-button>
      </div>

    </form>
  </modal>
</template>
<script>
import Modal from "@/Components/UI/Modal"
import AppInput from "@/Components/FormControls/Input"

export default {
  components: {
    AppInput,
    Modal
  },

  data() {
    return {
      form: this.freshForm()
    }
  },

  props: {
    show: {
      default: false
    },
    planType: {
      type: Object,
      default: null
    },
  },

  emits: [
    'close'
  ],

  watch: {
    show(val) {
      this.form = this.freshForm()
    }
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        name: this.planType?.name,
      })
    },
    submit() {
      if (this.planType) {
        this.form.put(this.$route('plan-types.update', this.planType.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('plan-types.store'), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },
}
</script>
