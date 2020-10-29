<template>
  <div>
    <app-layout v-if="$page.props.user">
      <template #header>{{ title }}</template>

      <div class="flex items-center my-12 h-full justify-center text-lg text-gray-500 tracking-wider">
        <div>{{ description }}</div>
      </div>
    </app-layout>

    <div v-else>
      <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
          <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
            <div class="ml-4 text-lg text-gray-500 uppercase tracking-wider">
              {{ description }}
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import AppLayout from '../Layouts/AppLayout'

export default {
  components: {
    AppLayout,
  },
  props: {
    status: Number,
  },
  computed: {
    title() {
      return {
        503: '503 - Service Unavailable',
        500: '500 - Server Error',
        404: '404 - Page Not Found',
        403: '403 - Forbidden',
        419: 'Page Expired'
      }[this.status]
    },
    description() {
      return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
        419: 'The page expired, please try again.'
      }[this.status]
    },
  },
}
</script>
