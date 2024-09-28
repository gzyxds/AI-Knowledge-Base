<template>
  <div class="flex flex-col justify-center items-center h-[60vh]">
    <el-image class="w-[200px] h-[200px]" :src="noAuth" />
    <div class="text-info">暂无内容～</div>
  </div>
</template>

<script setup lang="ts">
import noAuth from '@/assets/image/noAuth.png'
</script>

<style scoped lang="scss"></style>
