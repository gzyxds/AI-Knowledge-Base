<template>
  <div class="panel h-full">
    <slot />
  </div>
</template>

<script lang="ts" setup></script>
<style lang="scss" scoped>
.panel {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
