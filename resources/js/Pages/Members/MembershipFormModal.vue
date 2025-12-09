<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      {{ member.membership_since ? 'Edit Membership' : 'Create Membership'}}
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">

        <app-input
          id="dob"
          v-model="form.membership_since"
          :error="form.errors.membership_since"
          label="Membership Since"
          type="date"
          @update:model-value="form.clearErrors('membership_since')"
        />

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

        <div v-if="selectedPlanType && currentRate" class="p-3 bg-blue-50 border border-blue-200 rounded">
          <p class="text-sm text-gray-600">Current Membership Rate</p>
          <p class="text-lg font-semibold text-blue-600">${{ currentRate }}/month</p>
        </div>

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
import Modal from "@/Components/Modal";
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
        membership_since: this.member?.membership_since,
        membership_type: this.member?.membership_type,
        plan_type_id: this.member?.plan_type_id,
      })
    },
    'form.plan_type_id'() {
      // Trigger reactivity when plan type changes
      this.$forceUpdate()
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

  computed: {
    selectedPlanType() {
      if (!this.form.plan_type_id) {
        return null
      }
      return this.planTypes.find(p => p.id === parseInt(this.form.plan_type_id))
    },

    currentRate() {
      if (!this.selectedPlanType || !this.selectedPlanType.rates) {
        return null
      }

      const rates = this.selectedPlanType.rates
      
      // Handle both object and array formats
      if (Array.isArray(rates)) {
        return null
      }

      const today = new Date().toISOString().split('T')[0]
      const ratesObj = typeof rates === 'object' ? rates : JSON.parse(rates)
      
      // Get all dates and sort them
      const allDates = Object.keys(ratesObj).sort()
      
      // Find the most recent rate that is effective on or before today
      let applicableRate = null
      for (const date of allDates) {
        if (date <= today) {
          applicableRate = ratesObj[date]
        } else {
          break
        }
      }

      return applicableRate
    }
  },

  created() {
    this.$axios.get(this.$route('ajax.plan-types.index'))
      .then(response => {
        this.planTypes = response.data.plan_types
      })
  }
}
</script>
