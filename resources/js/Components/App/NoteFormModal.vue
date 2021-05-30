<template>
  <modal v-if="show" max-width="sm" @close="$emit('close')">

    <div class="px-6 py-4 text-xl font-medium">
      {{ note ? 'Edit Note' : 'Add Note'}}
    </div>

    <form @submit.prevent="submit">
      <div class="px-6 py-4 space-y-4">
        <app-input
          ref="content"
          id="content"
          v-model="form.content"
          :error="form.errors.content"
          type="textarea"
          @input="form.clearErrors('content')"
          class="-mt-3 h-24"
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
import Modal from "@/Components/UI/Modal";

export default {
  components: {
    Modal
  },

  data() {
    return {
      form: this.$inertia.form(),
    }
  },

  props: {
    show: Boolean,
    note: Object,
    noteableType: String,
    noteableId: [String, Number]
  },

  emits: ['close'],

  watch: {
    show(val) {
      this.form = this.$inertia.form({
        content: this.note?.content,
      })

      // if (val) {
        // setTimeout(() => this.$refs.content.focus(), 600)
      // }
    }
  },

  methods: {
    submit() {
      if (this.note) {
        this.form.put(this.$route('notes.update', this.note.id), {
          preserveScroll: true,
          onSuccess: () => this.$emit('close')
        })
      } else {
        this.form.post(this.$route('notes.store', {noteableType: this.noteableType, noteableId: this.noteableId}), {
          preserveScroll: true,
          onSuccess: () => this.$emit('close')
        })
      }
    },
  },
}
</script>
