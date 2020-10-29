<template>
    <div>
      <api-token-manager :tokens="tokens"
                         :available-permissions="availablePermissions"
                         :default-permissions="defaultPermissions"/>
    </div>
</template>

<script>
import ApiTokenManager from './ApiTokenManager'
import AppLayout from './../../Layouts/AppLayout'
import AppSectionBorder from '../../Shared/SectionBorder'

export default {
  layout: AppLayout,
  header: 'API Tokens',
  props: [
    'tokens',
    'availablePermissions',
    'defaultPermissions',
  ],

  components: {
    ApiTokenManager,
    AppLayout,
    AppSectionBorder,
  },
}
</script>
