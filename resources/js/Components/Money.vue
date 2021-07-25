<template>
  <div>
    <span :class="currencySignClass">$</span>{{ Number(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
  </div>
</template>

<script>
export default {
  props: {
    currencySignClass: {
      type: String,
      default: 'mr-0.5 text-gray-500'
    },
    amount: [Number, String]
  }
}
</script>
