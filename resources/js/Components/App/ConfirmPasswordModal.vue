<template>
  <modal v-if="show" :closeable="true" max-width="md" @close="close">
    <form @submit.prevent="submit">
      <div class="px-6 py-4">
        <div class="text-lg font-medium">
          Confirm Password
        </div>

        <div class="mt-4">
          Please confirm your password before continuing.

          <div class="mt-4">
            <app-input
              id="password"
              ref="password"
              v-model="form.password"
              :error="form.errors.password"
              autocomplete="current-password"
              autofocus
              placeholder="Password"
              required
              type="password"
              @input="form.clearErrors('password')"/>
          </div>
        </div>
      </div>

      <div class="px-6 py-4 bg-gray-100 flex items-center justify-end">
        <app-button type="button" color="secondary" @click="close">
          Cancel
        </app-button>

        <app-button :processing="form.processing" class="ml-4" type="submit">
          Confirm
        </app-button>
      </div>
    </form>
  </modal>
</template>

<script>
import Modal from "@/Components/UI/Modal";

export default {
  components: {
    Modal,
  },

  emits: [
    'close'
  ],

  props: {
    show: Boolean,
    redirectTo: {
      type: [String, Object],
      default: null
    }
  },

  data() {
    return {
      form: this.$inertia.form({
        password: '',
        redirect: !this.redirectTo
      }),
    }
  },

  watch: {
    show(val) {
      if (val) {
        setTimeout(() => this.$refs.password.focus(), 600)

      }
    }
  },

  methods: {
    close() {
      this.$emit('close')
    },

    submit() {
      this.form.post(this.$route('password.confirm'), {
        preserveScroll: true,
        onSuccess: () => {
          this.redirectTo ? this.$inertia.visit(this.redirectTo.url, this.redirectTo.options) : null
          this.close();
        }
      })
    }
  }
}
</script>
