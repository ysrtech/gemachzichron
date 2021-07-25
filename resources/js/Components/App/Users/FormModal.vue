<template>
  <modal v-if="show" max-width="md" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      <template v-if="user">Edit User</template>
      <template v-else>Create New User</template>
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

        <app-input
          id="email"
          v-model="form.email"
          :error="form.errors.email"
          label="Email"
          type="email"
          @update:model-value="form.clearErrors('email')"
        />

        <app-input
          id="password"
          v-model="form.password"
          :error="form.errors.password"
          label="Password"
          :placeholder="user ? 'Leave blank for no change' : ''"
          type="password"
          @update:model-value="form.clearErrors('password')"
        />

        <app-input
          id="password_confirmation"
          v-model="form.password_confirmation"
          :error="form.errors.password_confirmation"
          label="Confirm Password"
          type="password"
          @update:model-value="form.clearErrors('password_confirmation')"
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
import Modal from "@/Components/Modal"
import AppInput from "@/Components/FormControls/Input"

export default {
  components: {
    AppInput,
    Modal
  },

  data() {
    return {
      form: this.$inertia.form({
        name: this.user?.name,
        email: this.user?.email,
        password: '',
        password_confirmation: ''
      })
    }
  },

  props: {
    show: {
      default: false
    },
    user: {
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
        name: this.user?.name,
        email: this.user?.email,
        password: '',
        password_confirmation: ''
      })
    }
  },

  methods: {
    submit() {
      if (this.user) {
        this.form.put(this.$route('users.update', this.user.id), {
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('users.store'), {
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },
}
</script>
