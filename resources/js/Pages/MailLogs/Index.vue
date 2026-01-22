<template>
  <div class="max-w-12xl mx-auto">
    <div class="flex justify-between items-start mb-6">
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
      
      <div class="flex items-center space-x-2 ml-4">
        <a
          :href="$route('test-email')"
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition whitespace-nowrap"
        >
          <i class="material-icons-outlined text-base mr-2">send</i>
          Send Test
        </a>
      </div>
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
                  <div class="flex items-center space-x-3">
                    <div v-if="mail.open_count > 0" class="flex items-center space-x-1">
                      <i class="material-icons-outlined text-base">visibility</i>
                      <span>{{ mail.open_count }}</span>
                    </div>
                    <div v-if="mail.click_count > 0" class="flex items-center space-x-1">
                      <i class="material-icons-outlined text-base">ads_click</i>
                      <span>{{ mail.click_count }}</span>
                    </div>
                  </div>
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                  <button
                    @click="viewMail(mail)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    View
                  </button>
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

    <!-- Email Details Modal -->
    <div v-if="selectedMail" class="fixed z-10 inset-0 overflow-y-auto" @click="closeMail">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" @click.stop>
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-start mb-4">
              <div class="flex-1">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedMail.subject }}</h3>
                <span
                  class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                  :class="getStatusColor(selectedMail.status)"
                >
                  {{ selectedMail.status }}
                </span>
              </div>
              <button @click="closeMail" class="text-gray-400 hover:text-gray-500">
                <i class="material-icons-outlined">close</i>
              </button>
            </div>

            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 mb-6">
              <div>
                <dt class="text-sm font-medium text-gray-500">To</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  {{ selectedMail.to_name ? `${selectedMail.to_name} <${selectedMail.to_email}>` : selectedMail.to_email }}
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">From</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  {{ selectedMail.from_name ? `${selectedMail.from_name} <${selectedMail.from_email}>` : selectedMail.from_email }}
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Sent At</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(selectedMail.sent_at) }}</dd>
              </div>

              <div v-if="selectedMail.delivered_at">
                <dt class="text-sm font-medium text-gray-500">Delivered At</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(selectedMail.delivered_at) }}</dd>
              </div>

              <div v-if="selectedMail.opened_at">
                <dt class="text-sm font-medium text-gray-500">First Opened At</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(selectedMail.opened_at) }} ({{ selectedMail.open_count }} times)</dd>
              </div>

              <div v-if="selectedMail.clicked_at">
                <dt class="text-sm font-medium text-gray-500">First Clicked At</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(selectedMail.clicked_at) }} ({{ selectedMail.click_count }} times)</dd>
              </div>

              <div v-if="selectedMail.failed_at">
                <dt class="text-sm font-medium text-gray-500">Failed At</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(selectedMail.failed_at) }}</dd>
              </div>

              <div v-if="selectedMail.error_message" class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Error Message</dt>
                <dd class="mt-1 text-sm text-red-600">{{ selectedMail.error_message }}</dd>
              </div>
            </dl>

            <div>
              <dt class="text-sm font-medium text-gray-500 mb-2">Email Body</dt>
              <dd class="border border-gray-300 rounded-md p-4 bg-gray-50 max-h-96 overflow-y-auto">
                <div v-html="selectedMail.body"></div>
              </dd>
            </div>
          </div>
        </div>
      </div>
    </div>
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
      selectedMail: null,
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
    viewMail(mail) {
      this.selectedMail = mail;
    },

    closeMail() {
      this.selectedMail = null;
    },

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
