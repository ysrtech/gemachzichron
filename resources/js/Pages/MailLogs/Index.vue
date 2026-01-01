<template>
  <div class="max-w-12xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <search-filter
        v-model="filterForm.search"
        :applied-filters-length="appliedFiltersLength"
        class="w-full max-w-md"
        placeholder="Search by email or subject..."
        @reset="reset">

        <search-filter-field
          v-model="filterForm.status"
          type="select"
          label="Status"
          :options="statusOptions"
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
      
      <a
        :href="$route('test-email')"
        class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
      >
        <i class="material-icons-outlined text-base mr-2">send</i>
        Send Test Email
      </a>
    </div>

    <app-panel>
      <template #content>
        <div v-if="!mailLogs || !mailLogs.data || mailLogs.data.length === 0" class="px-6 py-10 text-center text-gray-500">
          No Mail Logs Found
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">To</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Subject</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sent At</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Activity</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="mail in mailLogs.data" :key="mail.id" class="hover:bg-gray-50">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                  <div class="font-medium text-gray-900">{{ mail.to_email }}</div>
                  <div v-if="mail.to_name" class="text-gray-500">{{ mail.to_name }}</div>
                </td>
                <td class="px-3 py-4 text-sm text-gray-900">
                  <div class="max-w-md truncate">{{ mail.subject }}</div>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                  <span
                    class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                    :class="getStatusColor(mail.status)"
                  >
                    {{ mail.status }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ formatDate(mail.sent_at) }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  <div v-if="mail.open_count > 0" class="flex items-center space-x-1">
                    <i class="material-icons-outlined text-base">visibility</i>
                    <span>{{ mail.open_count }}</span>
                  </div>
                  <div v-if="mail.click_count > 0" class="flex items-center space-x-1">
                    <i class="material-icons-outlined text-base">ads_click</i>
                    <span>{{ mail.click_count }}</span>
                  </div>
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                  <inertia-link
                    :href="$route('mail-logs.show', mail.id)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    View
                  </inertia-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="mailLogs && mailLogs.data && mailLogs.data.length > 0" class="bg-white px-4 py-3 flex items-center justify-around border-t border-gray-300 sm:px-6">
          <pagination :response="mailLogs" />
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
    mailLogs: Object,
    filters: Object,
  },

  data() {
    return {
      filterForm: {
        search: this.filters.search || '',
        status: this.filters.status || '',
        from_date: this.filters.from_date || '',
        to_date: this.filters.to_date || '',
      },
    };
  },

  computed: {
    statusOptions() {
      return {
        'All Statuses': '',
        'Sent': 'sent',
        'Delivered': 'delivered',
        'Opened': 'opened',
        'Clicked': 'clicked',
        'Failed': 'failed',
        'Bounced': 'bounced',
        'Complained': 'complained',
      };
    },
  },

  methods: {
    formatDate(dateString) {
      if (!dateString) return '-';
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

    getStatusColor(status) {
      const colors = {
        sent: 'bg-gray-100 text-gray-800',
        delivered: 'bg-blue-100 text-blue-800',
        opened: 'bg-green-100 text-green-800',
        clicked: 'bg-purple-100 text-purple-800',
        failed: 'bg-red-100 text-red-800',
        bounced: 'bg-orange-100 text-orange-800',
        complained: 'bg-yellow-100 text-yellow-800',
      };
      return colors[status] || 'bg-gray-100 text-gray-800';
    },
  },
};
</script>
