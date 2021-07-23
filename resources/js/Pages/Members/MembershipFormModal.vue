<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      {{ member.membership_since ? 'Edit Membership' : 'Create Membership'}}
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">

        <app-select
          native
          id="membership_type"
          v-model="form.membership_type"
          :error="form.errors.membership_type"
          label="Membership Type"
          :options="MEMBERSHIP_TYPES"
          @update:model-value="form.clearErrors('membership_type')"
        />

        <app-select
          native
          v-show="form.membership_type === MEMBERSHIP_TYPES.Membership"
          id="plan_type_id"
          v-model="form.plan_type_id"
          :error="form.errors.plan_type_id"
          label="Plan Type"
          @update:model-value="form.clearErrors('plan_type_id')"
        >
          <template #options>
            <option
              v-for="planType of planTypes"
              :value="planType.id">
              {{ planType.name }}
            </option>
          </template>
        </app-select>

        <label class="flex items-center">
          <app-checkbox v-model="form.active_membership" name="active_membership"/>
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
import AppCheckbox from "@/Components/FormControls/Checkbox";
import {MEMBERSHIP_TYPES} from "@/config/memberships";
import AppInput from "@/Components/FormControls/Input"
import AppSelect from "@/Components/FormControls/Select";

export default {
  components: {
    AppSelect,
    AppInput,
    AppCheckbox,
    Modal
  },

  data() {
    return {
      MEMBERSHIP_TYPES,
      planTypes: [],
      form: this.$inertia.form(),
    }
  },

  props: {
    show: Boolean,
    member: Object,
  },

  emits: [
    'close'
  ],

  watch: {
    show(val) {
      this.form = this.$inertia.form({
        active_membership: this.member.membership_since? this.member.active_membership : true,
        membership_type: this.member?.membership_type,
        plan_type_id: this.member?.plan_type_id,
      })
    }
  },

  methods: {
    submit() {
      this.form.put(this.$route('members.update', this.member.id), {
        onSuccess: () => this.$emit('close'),
        preserveState: 'errors',
      })
    }
  },

  created() {
    this.$axios.get(this.$route('plan-types.index'))
      .then(response => {
        this.planTypes = response.data.plan_types
      })
  }
}
</script>
