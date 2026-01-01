<template>
  <div class="max-w-4xl mx-auto">
    <div class="mb-4">
      <inertia-link :href="$route('mail-logs.index')" class="text-blue-600 hover:text-blue-800">
        ← Back to Mail Logs
      </inertia-link>
    </div>

    <app-panel>
      <template #content>
        <div class="px-6 py-6">
          <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ mailLog.subject }}</h2>
            <span
              class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
              :class="getStatusColor(mailLog.status)"
            >
              {{ mailLog.status }}
            </span>
          </div>

          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">To</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ mailLog.to_name ? `${mailLog.to_name} <${mailLog.to_email}>` : mailLog.to_email }}
              </dd>
            </div>

            <div>
              <dt class="text-sm font-medium text-gray-500">From</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ mailLog.from_name ? `${mailLog.from_name} <${mailLog.from_email}>` : mailLog.from_email }}
              </dd>
            </div>

            <div>
              <dt class="text-sm font-medium text-gray-500">Sent At</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(mailLog.sent_at) }}</dd>
            </div>

            <div v-if="mailLog.delivered_at">
              <dt class="text-sm font-medium text-gray-500">Delivered At</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(mailLog.delivered_at) }}</dd>
            </div>

            <div v-if="mailLog.opened_at">
              <dt class="text-sm font-medium text-gray-500">First Opened At</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(mailLog.opened_at) }} ({{ mailLog.open_count }} times)</dd>
            </div>

            <div v-if="mailLog.clicked_at">
              <dt class="text-sm font-medium text-gray-500">First Clicked At</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(mailLog.clicked_at) }} ({{ mailLog.click_count }} times)</dd>
            </div>

            <div v-if="mailLog.failed_at">
              <dt class="text-sm font-medium text-gray-500">Failed At</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(mailLog.failed_at) }}</dd>
            </div>

            <div v-if="mailLog.error_message" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Error Message</dt>
              <dd class="mt-1 text-sm text-red-600">{{ mailLog.error_message }}</dd>
            </div>

            <div class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500 mb-2">Email Body</dt>
              <dd class="mt-1 text-sm text-gray-900 border border-gray-300 rounded-md p-4 bg-gray-50 max-h-96 overflow-y-auto">
                <div v-html="mailLog.body"></div>
              </dd>
            </div>
          </dl>
        </div>
      </template>
    </app-panel>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import AppPanel from "@/Components/Panel";

export default {
  layout: AppLayout,
  components: {
    AppPanel,
  },

  props: {
    mailLog: Object,
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
