<template>
  <app-panel title="Notes">
    <template #actions>
      <button
        @click="openFormModal = true"
        title="Add Note"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
        add
      </button>
    </template>
    <template #content>
      <div class="p-4 sm:px-6 flex justify-between items-start group" v-for="note in notes">
        <pre class="font-sans text-gray-700 text-sm">{{ note.content }}</pre>
        <div class="bg-gray-100 rounded-full opacity-0 group-hover:opacity-100 flex text-gray-500">
          <button
            @click="openFormModal = true; noteToEdit = note"
            class="material-icons-outlined focus:outline-none rounded-full p-1 hover:text-gray-700 hover:bg-gray-200 focus:bg-gray-300">
            edit
          </button>
          <button
            @click="$inertia.delete($route('notes.destroy', note.id), {preserveScroll: true})"
            class="material-icons-outlined focus:outline-none rounded-full p-1 hover:text-gray-700 hover:bg-gray-200 focus:bg-gray-300">
            delete
          </button>
        </div>
      </div>
      <div class="px-4 py-10 sm:px-6 text-gray-400 text-center" v-show="notes.length === 0">
        No Notes
      </div>
      <note-form-modal
        :show="openFormModal"
        :note="noteToEdit"
        :noteable-type="noteableType"
        :noteable-id="noteableId"
        @close="openFormModal = false; noteToEdit = null"
      />
    </template>
  </app-panel>
</template>

<script>
import NoteFormModal from "@/Components/App/NoteFormModal";
import AppPanel from "@/Components/UI/Panel";

export default {

  components: {
    AppPanel,
    NoteFormModal
  },

  data() {
    return {
      openFormModal: false,
      noteToEdit: null
    }
  },

  props: {
    notes: Array,
    noteableType: String,
    noteableId: [String, Number]
  },
}
</script>
