<template>
  <div class="h-full flex flex-col">
    <div class="flex-1 min-h-0 max-w-[800px] w-full mx-auto">
      <ElScrollbar>
        <div class="p-main">
          <CenterTop />
          <CanvasDisplay class="mt-[16px]" />
          <CenterSetting class="mt-[16px]" />
        </div>
      </ElScrollbar>
    </div>
  </div>
</template>
<script setup lang="ts">
import CanvasDisplay from './canvas-display.vue'
import CenterTop from './center-top.vue'
import CenterSetting from './center-setting.vue'
</script>
