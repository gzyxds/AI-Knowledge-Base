<template>
  <div>
    <NuxtLayout name="default">
      <div class="h-full p-[16px] flex">
        <Form @update="recordRef?.refresh()" />
        <div class="flex-1 min-w-0 h-full pl-[16px]">
          <Record ref="recordRef" />
        </div>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
import Form from './_components/form/index.vue'
import Record from './_components/record/index.vue'

const recordRef = shallowRef()
definePageMeta({
  layout: false,
  hiddenFooter: true
})
</script>
