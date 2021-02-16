<template>
  <modal :show="show" max-width="sm" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="dependent">Edit Dependent</template>
      <template v-else>Add Dependent</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">
        <app-input
          id="first_name"
          v-model="form.first_name"
          :error="form.errors.first_name"
          autocomplete="xxx"
          label="First Name"
          type="text"
          @input="form.clearErrors('first_name')"
        />

        <app-input
          id="last_name"
          v-model="form.last_name"
          :error="form.errors.last_name"
          autocomplete="xxx"
          label="Last Name"
          type="text"
          @input="form.clearErrors('last_name')"
        />

        <app-input
          id="hebrew_name"
          v-model="form.hebrew_name"
          :error="form.errors.hebrew_name"
          autocomplete="xxx"
          label="Hebrew Name"
          type="text"
          @input="form.clearErrors('hebrew_name')"
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
      </div>

      <div class="px-6 py-3 bg-gray-100 flex items-center justify-end">
        <app-button color="secondary" @click="$emit('close')">Cancel</app-button>
        <app-button :processing="form.processing" class="ml-2" color="primary" type="submit">Submit</app-button>
      </div>

    </form>
  </modal>
</template>
<script>
import Modal from "@/Components/UI/Modals/Modal";

export default {
  components: {
    Modal
  },

  data() {
    return {
      form: this.$inertia.form({
        first_name: this.dependent?.first_name,
        last_name: this.dependent?.last_name,
        hebrew_name: this.dependent?.hebrew_name,
        dob: this.dependent?.dob,
      })
    }
  },

  props: {
    show: {
      default: false
    },
    member: Object,
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
      this.form = this.$inertia.form({
        first_name: this.dependent?.first_name,
        last_name: this.dependent?.last_name,
        hebrew_name: this.dependent?.hebrew_name,
        dob: this.dependent?.dob,
      })
    }
  },

  methods: {
    submit() {
      if (this.dependent) {
        this.form.put(this.$route('dependents.update', this.dependent.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('members.dependents.store', this.member.id), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },
}
</script>
