<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="dependent">Edit Child</template>
      <template v-else>Add Child</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">
        <app-input
          id="name"
          v-model="form.name"
          :error="form.errors.name"
          autocomplete="xxx"
          label="Name"
          type="text"
          @input="form.clearErrors('name')"
        />

        <app-input
          id="dob"
          v-model="form.dob"
          :error="form.errors.dob"
          autocomplete="xxx"
          label="Date of Birth"
          type="date"
          @input="form.clearErrors('dob')"
        />

        <label class="flex items-center">
          <app-checkbox v-model="form.is_married" name="is_married"/>
          <span class="ml-2 text-sm text-gray-600">Married</span>
        </label>
      </div>

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-end">
        <app-button color="secondary" @click="$emit('close')">Cancel</app-button>
        <app-button :processing="form.processing" class="ml-2" color="primary" type="submit">Submit</app-button>
      </div>

    </form>
  </modal>
</template>
<script>
import Modal from "@/Components/UI/Modal";
import AppCheckbox from "@/Components/UI/Checkbox";

export default {
  components: {
    AppCheckbox,
    Modal
  },

  data() {
    return {
      form: this.freshForm()
    }
  },

  props: {
    show: Boolean,
    memberId: Number,
    dependent: {
      type: Object,
      default: null
    },
  },

  emits: [
    'close'
  ],

  watch: {
    show(val) {
      // this.form.reset()
      this.form = this.freshForm()
    }
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        name: this.dependent?.name || '',
        dob: this.dependent?.dob || '',
        is_married: this.dependent?.is_married || false
      })
    },

    submit() {
      if (this.dependent) {
        this.form.put(this.$route('dependents.update', this.dependent.id), {
          onSuccess: () => this.$emit('close'),
          preserveScroll: true
        })
      } else {
        this.form.post(this.$route('members.dependents.store', this.memberId), {
          onSuccess: () => this.$emit('close'),
          preserveScroll: true
        })
      }
    },
  },
}
</script>
