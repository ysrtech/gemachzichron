<template>
  <app-panel title="Comments">
    <template #actions>
      <button
        @click="openFormModal = true"
        title="Add Comment"
        class="material-icons-outlined focus:outline-none rounded-full p-1.5 text-gray-600 hover:bg-gray-200 focus:bg-gray-300">
        add
      </button>
    </template>
    <template #content>
      <div class="p-4 sm:px-6 flex justify-between items-start group" v-for="comment in comments">
        <span class="text-gray-700 text-sm">{{ comment.content }}</span>
        <div class="bg-gray-100 rounded-full opacity-0 group-hover:opacity-100 flex text-gray-500">
          <button
            @click="openFormModal = true; commentToEdit = comment"
            class="material-icons-outlined focus:outline-none rounded-full p-1 hover:text-gray-700 hover:bg-gray-200 focus:bg-gray-300">
            edit
          </button>
          <button
            @click="$inertia.delete($route('comments.destroy', comment.id), {preserveScroll: true})"
            class="material-icons-outlined focus:outline-none rounded-full p-1 hover:text-gray-700 hover:bg-gray-200 focus:bg-gray-300">
            delete
          </button>
        </div>
      </div>
      <div class="px-4 py-10 sm:px-6 text-gray-400 text-center" v-show="comments.length === 0">
        No Comments
      </div>
      <comment-form-modal
        :show="openFormModal"
        :comment="commentToEdit"
        :commentable-type="commentableType"
        :commentable-id="commentableId"
        @close="openFormModal = false; commentToEdit = null"
      />
    </template>
  </app-panel>
</template>

<script>
import CommentFormModal from "@/Components/App/CommentFormModal";
import AppPanel from "@/Components/UI/Panel";

export default {

  components: {
    AppPanel,
    CommentFormModal
  },

  data() {
    return {
      openFormModal: false,
      commentToEdit: null
    }
  },

  props: {
    comments: Array,
    commentableType: String,
    commentableId: [String, Number]
  },
}
</script>
