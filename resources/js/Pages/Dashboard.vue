<template>
  <main class="flex-1 relative pb-2 z-0 overflow-y-auto max-w-6xl mx-auto">
    <div>
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          <div class="bg-gray-300 w-36 h-5 rounded-full"></div>
        </h2>
        <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <div class="bg-white overflow-hidden shadow rounded-lg" v-for="n in 3">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="bg-gray-300 rounded-full h-8 w-8"></div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-xs font-medium text-gray-500 truncate">
                      <div class="bg-gray-300 w-1/2 h-4 rounded-full"></div>
                    </dt>
                    <dd>
                      <div class="text-xs font-medium text-gray-900 mt-2">
                        <div class="bg-gray-300 w-1/3 h-4 rounded-full"></div>
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
              <div class="text-sm">
                <a href="#" class="font-medium text-cyan-700 hover:text-cyan-900">
                  <div class="bg-gray-300 hover:bg-gray-400 w-1/3 h-4 rounded-full"></div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <h2 class="max-w-6xl mx-auto mt-8 px-4 text-lg leading-6 font-medium text-gray-900 sm:px-6 lg:px-8">
        <div class="bg-gray-300 w-36 h-5 rounded-full"></div>
      </h2>

      <!-- Activity list (smallest breakpoint only) -->
      <div class="shadow sm:hidden">
        <ul class="mt-3 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">
          <li v-for="n in 6">
            <a href="#" class="block px-4 py-4 bg-white hover:bg-gray-50">
              <span class="flex items-center space-x-4">
                <span class="flex-1 flex space-x-2 truncate">
                  <div class="bg-gray-300 rounded-full h-6 w-6"></div>
                  <span class="flex flex-col text-gray-500 truncate space-y-2">
                    <span class="truncate">
                      <div class="bg-gray-300 w-28 rounded-full">&nbsp;</div>
                    </span>
                    <span>
                      <div class="bg-gray-300 w-20 rounded-full">&nbsp;</div>
                    </span>
                    <span>
                      <div class="bg-gray-300 w-24 rounded-full">&nbsp;</div>
                    </span>
                  </span>
                </span>
                <i class="material-icons-outlined text-gray-400">keyboard_arrow_right</i>
              </span>
            </a>
          </li>
        </ul>
      </div>

      <div class="hidden sm:block">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex flex-col mt-3">
            <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="bg-gray-300 w-48 h-4 rounded-full"></div>
                  </th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="bg-gray-300 w-24 h-4 rounded-full"></div>
                  </th>
                  <th class="hidden px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider md:block">
                    <div class="bg-gray-300 w-24 h-4 rounded-full"></div>
                  </th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="bg-gray-300 w-24 h-4 rounded-full"></div>
                  </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr class="bg-white" v-for="n in 15">
                  <td class="max-w-0 w-full px-6 py-4 whitespace-nowrap text-gray-900">
                    <div class="flex">
                      <a href="#" class="group inline-flex space-x-2 truncate">
                        <div class="bg-gray-300 rounded-full h-5 w-5"></div>
                        <span class="bg-gray-300 w-48 h-5 rounded-full"></span>
                      </a>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-right whitespace-nowrap text-gray-500">
                    <div class="bg-gray-300 w-36 h-4 rounded-full"></div>
                  </td>
                  <td class="hidden px-6 py-4 whitespace-nowrap text-gray-500 md:block">
                    <div class="bg-gray-300 w-34 h-4 rounded-full"></div>
                  </td>
                  <td class="px-6 py-4 text-right whitespace-nowrap text-gray-500">
                    <div class="bg-gray-300 w-36 h-4 rounded-full"></div>
                  </td>
                </tr>
                </tbody>
              </table>
              <!-- Pagination -->
              <nav class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                   aria-label="Pagination">
                <div class="hidden sm:block">
                  <div class="bg-gray-300 w-36 h-4 rounded-full"></div>
                </div>
                <div class="flex-1 flex justify-between sm:justify-end">
                  <app-button color="secondary">
                    <div class="bg-gray-300 w-14 h-3 rounded-full"></div>
                  </app-button>
                  <app-button color="secondary" class="ml-3">
                    <div class="bg-gray-300 w-14 h-3 rounded-full"></div>
                  </app-button>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
  layout: (h, page) => h(AppLayout, {header: 'Dashboard'}, () => page),
}
</script>
