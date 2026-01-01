<template>
  <member-base :member="member">
    <div class="grid grid-cols-2 gap-6" style="height: calc(100vh - 250px);">
      <!-- Left Side: Activity Log -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg flex flex-col">
        <div class="px-4 py-5 sm:px-6 flex-shrink-0">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Activity Log
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Automatic timeline of all changes
          </p>
        </div>

        <div class="border-t border-gray-200 flex-1 overflow-y-auto">
          <div v-if="!activities || !activities.data || activities.data.length === 0" class="px-4 py-12 text-center text-gray-500">
            No activity recorded yet
          </div>

          <div v-else class="flow-root px-6 py-6">
            <ul role="list" class="-mb-8">
              <li v-for="(activity, activityIdx) in activities.data" :key="activity.id">
                <div class="relative pb-8">
                  <span
                    v-if="activityIdx !== activities.data.length - 1"
                    class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                    aria-hidden="true"
                  />
                  <div class="relative flex space-x-3">
                    <div>
                      <span
                        class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                        :class="getActivityColor(activity.description || activity.event)"
                      >
                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path v-if="(activity.description || activity.event) === 'created' || (activity.description && activity.description.includes('created'))" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                          <path v-else-if="(activity.description || activity.event) === 'updated' || (activity.description && activity.description.includes('modified'))" fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                          <path v-else fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                      </span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-1">
                          <span class="font-medium text-gray-900 text-sm">
                            {{ getActivityDescription(activity) }}
                          </span>
                          <span class="text-xs text-gray-500">
                            {{ formatDate(activity.created_at) }}
                          </span>
                          <span v-if="activity.causer" class="text-xs text-gray-500">
                            by {{ activity.causer.name }}
                          </span>
                        </div>
                        <button
                          v-if="hasChanges(activity)"
                          @click="toggleActivity(activity.id)"
                          class="text-gray-400 hover:text-gray-600 ml-2">
                          <svg class="h-5 w-5 transform transition-transform" :class="{ 'rotate-180': expandedActivities[activity.id] }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                        </button>
                      </div>
                      <div v-if="hasChanges(activity) && expandedActivities[activity.id]" class="mt-2 text-sm text-gray-700">
                        <div class="bg-gray-50 rounded-md p-3 space-y-1">
                          <div v-for="(change, key) in getChanges(activity)" :key="key" class="flex items-start">
                            <span class="font-medium text-gray-600 min-w-[120px]">{{ formatFieldName(key) }}:</span>
                            <div class="flex-1">
                              <span v-if="change.old !== null && change.old !== undefined" class="text-red-600 line-through">{{ formatValue(change.old) }}</span>
                              <span v-if="change.old !== null && change.old !== undefined && change.new !== null && change.new !== undefined"> → </span>
                              <span v-if="change.new !== null && change.new !== undefined" class="text-green-600">{{ formatValue(change.new) }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="activities && activities.data && activities.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 flex-shrink-0">
          <pagination :response="activities" />
        </div>
      </div>

      <!-- Right Side: Notes -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg flex flex-col">
        <div class="px-4 py-5 sm:px-6 flex-shrink-0">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Notes
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Add and view manual notes
          </p>
        </div>

        <div class="border-t border-gray-200 flex-1 overflow-y-auto">
          <div class="p-6">
            <!-- Add Note Form -->
            <form @submit.prevent="submitNote" class="mb-6 sticky top-0 bg-white z-10 pb-4">
              <div>
                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                  Add a Note
                </label>
                <textarea
                  id="note"
                  v-model="form.note"
                  rows="4"
                  class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-2 border-gray-300 rounded-md p-3"
                  placeholder="Enter your note here..."
                  required
                ></textarea>
              </div>
              <div class="mt-3 flex justify-end">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50">
                  <span v-if="!form.processing">Save Note</span>
                  <span v-else>Saving...</span>
                </button>
              </div>
            </form>

            <!-- Notes List -->
            <div class="border-t border-gray-200 pt-6">
              <div v-if="notes.length === 0" class="text-center text-gray-500 py-8">
                No notes yet
              </div>

              <div v-else class="space-y-4">
              <div
                v-for="note in notes"
                :key="note.id"
                class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex justify-between items-start mb-2">
                  <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-gray-900">{{ note.user.name }}</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-500">{{ formatDate(note.created_at) }}</span>
                    <button
                      @click="editNote(note)"
                      class="text-gray-400 hover:text-primary-600"
                      title="Edit note">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="deleteNote(note)"
                      class="text-gray-400 hover:text-red-600"
                      title="Delete note">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
                <div v-if="editingNoteId === note.id">
                  <textarea
                    v-model="editForm.note"
                    rows="4"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-2 border-gray-300 rounded-md mb-2 p-3"
                  ></textarea>
                  <div class="flex justify-end space-x-2">
                    <button
                      @click="cancelEdit"
                      type="button"
                      class="px-3 py-1 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                      Cancel
                    </button>
                    <button
                      @click="updateNote(note)"
                      type="button"
                      :disabled="editForm.processing"
                      class="px-3 py-1 text-sm border border-transparent rounded-md text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50">
                      Save
                    </button>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-700 whitespace-pre-wrap">{{ note.note }}</p>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </member-base>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import MemberBase from "@/Pages/Members/MemberBase";
import Pagination from "@/Components/Pagination";

export default {
  components: {
    MemberBase,
    Pagination,
  },

  layout: AppLayout,

  props: {
    member: Object,
    activities: {
      type: Object,
      default: () => ({ data: [] })
    },
    notes: {
      type: Array,
      default: () => []
    },
  },

  data() {
    return {
      form: this.$inertia.form({
        note: '',
      }),
      editingNoteId: null,
      editForm: this.$inertia.form({
        note: '',
      }),
      expandedActivities: {},
    };
  },

  methods: {
    submitNote() {
      this.form.post(this.$route('members.activities.store', { member: this.member.id }), {
        preserveScroll: true,
        onSuccess: () => {
          this.form.reset();
        },
      });
    },

    editNote(note) {
      this.editingNoteId = note.id;
      this.editForm.note = note.note;
    },

    cancelEdit() {
      this.editingNoteId = null;
      this.editForm.reset();
    },

    updateNote(note) {
      this.editForm.put(this.$route('members.activities.update', { member: this.member.id, note: note.id }), {
        preserveScroll: true,
        onSuccess: () => {
          this.editingNoteId = null;
          this.editForm.reset();
        },
      });
    },

    deleteNote(note) {
      if (confirm('Are you sure you want to delete this note?')) {
        this.$inertia.delete(this.$route('members.activities.destroy', { member: this.member.id, note: note.id }), {
          preserveScroll: true,
        });
      }
    },

    toggleActivity(activityId) {
      this.expandedActivities[activityId] = !this.expandedActivities[activityId];
    },
    formatDate(date) {
      return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      });
    },

    getActivityColor(event) {
      // Check for custom descriptions with keywords
      if (event && event.includes('created')) {
        return 'bg-green-500';
      }
      if (event && event.includes('modified')) {
        return 'bg-blue-500';
      }
      
      const colors = {
        created: 'bg-green-500',
        updated: 'bg-blue-500',
        deleted: 'bg-red-500',
      };
      return colors[event] || 'bg-gray-500';
    },

    getActivityIcon(event) {
      // Icons are rendered inline in the SVG
      return '';
    },

    getActivityDescription(activity) {
      // Check if there's a custom description (for manual logs like "Manual transaction created")
      if (activity.description && !['created', 'updated', 'deleted'].includes(activity.description)) {
        return activity.description;
      }
      
      const subjectType = activity.subject_type ? activity.subject_type.split('\\').pop() : 'Item';
      const event = activity.description || activity.event || 'modified';
      const subjectId = activity.subject_id || '';
      
      // Always build descriptive string with type, ID, and event
      return `${subjectType} #${subjectId} ${event}`;
    },

    hasChanges(activity) {
      return activity.properties && (activity.properties.attributes || activity.properties.old);
    },

    getChanges(activity) {
      if (!activity.properties) return {};
      
      const attributes = activity.properties.attributes || {};
      const old = activity.properties.old || {};
      
      const changes = {};
      const allKeys = new Set([...Object.keys(attributes), ...Object.keys(old)]);
      
      allKeys.forEach(key => {
        if (key !== 'updated_at' && key !== 'created_at') {
          changes[key] = {
            old: old[key],
            new: attributes[key]
          };
        }
      });
      
      return changes;
    },

    formatFieldName(fieldName) {
      return fieldName
        .replace(/_/g, ' ')
        .replace(/\b\w/g, char => char.toUpperCase());
    },

    formatValue(value) {
      if (value === null || value === undefined || value === '') {
        return '(empty)';
      }
      if (typeof value === 'boolean') {
        return value ? 'Yes' : 'No';
      }
      return value;
    },
  },
};
</script>
