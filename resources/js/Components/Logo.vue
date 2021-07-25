<template>
    <img src="/img/logo.png" alt="Gemach Hakehilos">
    <!--  <svg viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" class="fill-current" :class="colorClass">-->
    <!--    <text dominant-baseline="middle" font-family="sans-serif" font-size="20px" text-anchor="middle" x="50%" y="50%">Gemach Hakehilos</text>-->
    <!--  </svg>-->
</template>

<script>
export default {
    name: 'AppLogo',

    props: {
        colorClass: {
            default: 'text-primary-500'
        }
    }
}
</script>

