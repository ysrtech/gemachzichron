<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      {{ membership ? 'Edit Membership' : 'Create Membership'}}
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">

        <app-input
          id="type"
          v-model="form.type"
          :error="form.errors.type"
          label="Membership Type"
          type="select"
          @input="form.clearErrors('type')"
        >
          <template #options>
            <option>Membership</option>
            <option>Pekudon</option>
          </template>
        </app-input>

        <app-input
          v-if="form.type === 'Membership'"
          id="plan_type_id"
          v-model="form.plan_type_id"
          :error="form.errors.plan_type_id"
          label="Plan Type"
          type="select"
          @input="form.clearErrors('plan_type_id')"
        >
          <template #options>
            <option
              v-for="planType of planTypes"
              :value="planType.id">
              {{ planType.name }}
            </option>
          </template>
        </app-input>

        <label class="flex items-center">
          <app-checkbox v-model="form.is_active" name="is_active"/>
          <span class="ml-2 text-sm text-gray-600">Active</span>
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
      planTypes: [],
      form: this.$inertia.form(),
    }
  },

  props: {
    show: Boolean,
    memberId: Number,
    membership: Object,
  },

  emits: [
    'close'
  ],

  watch: {
    show(val) {
      this.form = this.$inertia.form({
        is_active: this.membership? this.membership.is_active : true,
        type: this.membership?.type,
        plan_type_id: this.membership?.plan_type_id,
      })
    }
  },

  methods: {
    submit() {
      if (this.membership) {
        this.form.put(this.$route('memberships.update', this.membership.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('members.membership.store', this.memberId), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },

  created() {
    this.$axios.get(this.$route('plan-types.index'))
      .then(response => {
        this.planTypes = response.data.plan_types
      })
  }
}
</script>
