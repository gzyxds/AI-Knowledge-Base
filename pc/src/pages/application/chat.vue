<template>
  <div>
    <NuxtLayout name="default">
      <div class="h-full flex">
        <div class="flex h-full p-[16px]">
          <TheSession
            v-model="robotStore.sessionId"
            :data="robotStore.sessionLists"
            @add="robotStore.sessionAdd"
            @edit="robotStore.sessionEdit"
            @delete="robotStore.sessionDelete"
            @clear="robotStore.sessionClear"
            @click-item="robotStore.setSessionSelect"
          />
        </div>
        <div class="h-full pr-[16px] py-[16px] flex-1 min-w-0">
          <div class="h-full flex flex-col bg-body rounded-lg">
            <TheChat :robot-id="robotId" />
          </div>
        </div>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
import { useRobotStore } from '@/stores/robot'

const robotStore = useRobotStore()
const route = useRoute()
const robotId = computed(() => route.query.id as string)
definePageMeta({
  auth: true,
  layout: false,
  hasPanel: true,
  hiddenFooter: true,
  activePath: '/application/layout/robot'
})
</script>

<style lang="scss" scoped></style>
