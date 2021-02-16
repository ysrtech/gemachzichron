<template>
  <modal :show="show" max-width="xl" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="member">Edit Member</template>
      <template v-else>Create New Member</template>
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 grid grid-cols-2 gap-y-4 gap-x-6">
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
          id="wife_name"
          v-model="form.wife_name"
          :error="form.errors.wife_name"
          autocomplete="xxx"
          label="Wife's Name"
          type="text"
          @input="form.clearErrors('wife_name')"
        />

        <app-input
          id="email"
          v-model="form.email"
          :error="form.errors.email"
          autocomplete="xxx"
          label="Email"
          type="email"
          @input="form.clearErrors('email')"
        />

        <app-input
          id="home_phone"
          v-model="form.home_phone"
          :error="form.errors.home_phone"
          autocomplete="xxx"
          label="Home Phone"
          type="text"
          @input="form.clearErrors('home_phone')"
        />

        <app-input
          id="mobile_phone"
          v-model="form.mobile_phone"
          :error="form.errors.mobile_phone"
          autocomplete="xxx"
          label="Cellphone"
          type="text"
          @input="form.clearErrors('mobile_phone')"
        />

        <app-input
          id="shtibel"
          v-model="form.shtibel"
          :error="form.errors.shtibel"
          autocomplete="xxx"
          label="Shtibel"
          type="text"
          @input="form.clearErrors('shtibel')"
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
        first_name: this.member?.first_name || '',
        last_name: this.member?.last_name || '',
        hebrew_name: this.member?.hebrew_name || '',
        wife_name: this.member?.wife_name || '',
        email: this.member?.email || '',
        home_phone: this.member?.home_phone || '',
        mobile_phone: this.member?.mobile_phone || '',
        shtibel: this.member?.shtibel || '',
      })
    }
  },

  props: {
    show: {
      default: false
    },
    member: {
      type: Object,
      default: null
    }
  },

  emits: [
    'close'
  ],

  watch: {
    show(val) {
      // this.form.reset()
      this.form = this.$inertia.form({
        first_name: this.member?.first_name || '',
        last_name: this.member?.last_name || '',
        hebrew_name: this.member?.hebrew_name || '',
        wife_name: this.member?.wife_name || '',
        email: this.member?.email || '',
        home_phone: this.member?.home_phone || '',
        mobile_phone: this.member?.mobile_phone || '',
        shtibel: this.member?.shtibel || '',
      })
    }
  },

  methods: {
    submit() {
      if (this.member) {
        this.form.put(this.$route('members.update', this.member.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('members.store'), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },
}
</script>
