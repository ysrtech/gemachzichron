<template>
  <app-layout>
    <template #header>
      <h2 class="font-medium text-2xl text-gray-800 leading-tight">
        API Tokens
      </h2>
    </template>

    <div>
      <api-token-manager :tokens="tokens"
                         :available-permissions="availablePermissions"
                         :default-permissions="defaultPermissions"/>
    </div>
  </app-layout>
</template>

<script>
import ApiTokenManager from './ApiTokenManager'
import AppLayout from './../../Layouts/AppLayout'
import JetSectionBorder from '../../Shared/SectionBorder'

export default {
  props: [
    'tokens',
    'availablePermissions',
    'defaultPermissions',
  ],

  components: {
    ApiTokenManager,
    AppLayout,
    JetSectionBorder,
  },
}
</script>
