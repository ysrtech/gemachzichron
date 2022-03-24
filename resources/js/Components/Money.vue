<template>
  <div>
    <span :class="currencySignClass">$</span>{{ Number(amount).toLocaleString(undefined, {minimumFractionDigits: fraction, maximumFractionDigits: fraction}) }}
  </div>
</template>

<script>
export default {
  props: {
    currencySignClass: {
      type: String,
      default: 'mr-0.5 text-gray-500'
    },
    amount: [Number, String],
    fraction: {
      type: Number,
      default: 2
    }
  }
}
</script>
