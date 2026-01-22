<template>
  <main class="overflow-y-auto max-w-7xl mx-auto">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-zinc-950/10">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="material-icons-outlined text-3xl text-sky-500">account_balance_wallet</i>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-zinc-500 truncate">Total Revenue</dt>
                <dd class="text-2xl font-semibold text-zinc-900">$45,231</dd>
              </dl>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center text-sm">
              <span class="text-green-600 font-medium">+12.5%</span>
              <span class="ml-2 text-zinc-500">from last month</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-zinc-950/10">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="material-icons-outlined text-3xl text-sky-500">people</i>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-zinc-500 truncate">Active Users</dt>
                <dd class="text-2xl font-semibold text-zinc-900">1,429</dd>
              </dl>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center text-sm">
              <span class="text-green-600 font-medium">+5.4%</span>
              <span class="ml-2 text-zinc-500">from last month</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-zinc-950/10">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="material-icons-outlined text-3xl text-sky-500">receipt_long</i>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-zinc-500 truncate">Transactions</dt>
                <dd class="text-2xl font-semibold text-zinc-900">234</dd>
              </dl>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center text-sm">
              <span class="text-red-600 font-medium">-2.1%</span>
              <span class="ml-2 text-zinc-500">from last month</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-zinc-950/10">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="material-icons-outlined text-3xl text-sky-500">trending_up</i>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-zinc-500 truncate">Growth Rate</dt>
                <dd class="text-2xl font-semibold text-zinc-900">24.7%</dd>
              </dl>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center text-sm">
              <span class="text-green-600 font-medium">+8.2%</span>
              <span class="ml-2 text-zinc-500">from last month</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
      <!-- Revenue Chart -->
      <div class="bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-zinc-950/10">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-zinc-900 mb-6">Revenue Overview</h3>
          <div class="h-64 flex items-end justify-between space-x-2">
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 45%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 60%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 55%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 75%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 65%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 85%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 90%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 80%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 95%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 100%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 85%"></div>
            <div class="flex-1 bg-sky-500 rounded-t" style="height: 92%"></div>
          </div>
          <div class="mt-4 flex justify-between text-sm text-zinc-500">
            <span>Jan</span>
            <span>Feb</span>
            <span>Mar</span>
            <span>Apr</span>
            <span>May</span>
            <span>Jun</span>
            <span>Jul</span>
            <span>Aug</span>
            <span>Sep</span>
            <span>Oct</span>
            <span>Nov</span>
            <span>Dec</span>
          </div>
        </div>
      </div>

      <!-- User Activity Chart -->
      <div class="bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-zinc-950/10">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-zinc-900 mb-6">User Activity</h3>
          <div class="h-64 flex items-center justify-center">
            <div class="relative w-48 h-48">
              <!-- Donut Chart Placeholder -->
              <svg viewBox="0 0 100 100" class="w-full h-full">
                <circle cx="50" cy="50" r="40" fill="none" stroke="#e5e7eb" stroke-width="20"/>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#0ea5e9" stroke-width="20" 
                  stroke-dasharray="188.5" stroke-dashoffset="47" transform="rotate(-90 50 50)"/>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#14b8a6" stroke-width="20" 
                  stroke-dasharray="188.5" stroke-dashoffset="141" transform="rotate(-90 50 50)"/>
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <div class="text-2xl font-semibold text-zinc-900">75%</div>
                  <div class="text-sm text-zinc-500">Active</div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6 space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-sky-500 mr-2"></div>
                <span class="text-sm text-zinc-600">Active Users</span>
              </div>
              <span class="text-sm font-medium text-zinc-900">75%</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-teal-500 mr-2"></div>
                <span class="text-sm text-zinc-600">Inactive Users</span>
              </div>
              <span class="text-sm font-medium text-zinc-900">25%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8">
      <div class="bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-zinc-950/10">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-zinc-900 mb-6">Recent Activity</h3>
          <div class="space-y-4">
            <div v-for="i in 5" :key="i" class="flex items-center justify-between py-3 border-b border-zinc-100 last:border-0">
              <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center">
                  <i class="material-icons-outlined text-sky-600 text-xl">{{ ['person_add', 'receipt', 'credit_card', 'email', 'settings'][i-1] }}</i>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-zinc-900">{{ ['New user registered', 'Transaction completed', 'Payment received', 'Email sent', 'Settings updated'][i-1] }}</p>
                  <p class="text-sm text-zinc-500">{{ [2, 5, 15, 30, 45][i-1] }} minutes ago</p>
                </div>
              </div>
              <span class="text-sm text-zinc-500">{{ ['User #' + (1234 + i), '$' + (500 + i * 100), '$' + (250 + i * 50), 'Newsletter', 'Admin'][i-1] }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  layout: (h, page) => h(AppLayout, { title: "Dashboard" }, () => page),

  name: 'Dashboard',
}
</script>
