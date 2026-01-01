<template>
  <div class="max-w-12xl mx-auto">
    <search-filter
      v-model="filterForm.search"
      :applied-filters-length="appliedFiltersLength"
      class="w-full max-w-md mb-6"
      placeholder=""
      @reset="reset">

      <search-filter-field
        v-model="filterForm.user_id"
        type="select"
        label="Filter by User"
        :options="userOptions"
      />

      <search-filter-field
        v-model="filterForm.model_type"
        type="select"
        label="Filter by Model"
        :options="modelOptions"
      />

      <search-filter-field
        v-model="filterForm.from_date"
        type="date"
        label="From Date"
      />

      <search-filter-field
        v-model="filterForm.to_date"
        type="date"
        label="To Date"
      />
    </search-filter>

    <app-panel>
      <template #content>
        <div v-if="!activities || !activities.data || activities.data.length === 0" class="px-6 py-10 text-center text-gray-500">
          No Activity Logs Found
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
                        <inertia-link
                          v-if="activity.member_id"
                          :href="$route('members.activities.index', activity.member_id)"
                          class="text-xs text-blue-600 hover:text-blue-800"
                        >
                          View Member
                        </inertia-link>
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

        <!-- Pagination -->
        <div v-if="activities && activities.data && activities.data.length > 0" class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
          <pagination :response="activities" />
        </div>
      </template>
    </app-panel>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import AppPanel from "@/Components/Panel";
import SearchFilter from "@/Components/SearchFilter";
import SearchFilterField from "@/Components/SearchFilterField";
import HasFilters from "@/Mixins/HasFilters";

export default {
  layout: AppLayout,
  components: {
    Pagination,
    AppPanel,
    SearchFilter,
    SearchFilterField,
  },
  mixins: [HasFilters],

  props: {
    activities: Object,
    users: Array,
    modelTypes: Array,
    filters: Object,
  },

  data() {
    return {
      expandedActivities: {},
      filterForm: {
        search: this.filters.search || '',
        user_id: this.filters.user_id || '',
        model_type: this.filters.model_type || '',
        from_date: this.filters.from_date || '',
        to_date: this.filters.to_date || '',
      },
    };
  },

  computed: {
    userOptions() {
      const options = { 'All Users': '' };
      this.users.forEach(user => {
        options[user.name] = user.id;
      });
      return options;
    },

    modelOptions() {
      const options = { 'All Models': '' };
      this.modelTypes.forEach(type => {
        options[type.label] = type.value;
      });
      return options;
    },
  },

  methods: {
    toggleActivity(activityId) {
      this.expandedActivities[activityId] = !this.expandedActivities[activityId];
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
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
      if (event && event.includes('deleted')) {
        return 'bg-red-500';
      }
      
      const colors = {
        created: 'bg-green-500',
        updated: 'bg-blue-500',
        deleted: 'bg-red-500',
      };
      return colors[event] || 'bg-gray-500';
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
