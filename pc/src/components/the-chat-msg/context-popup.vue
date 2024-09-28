<template>
  <ElDialog
    :title="`对话上下文(${context.length}条)`"
    :model-value="true"
    width="700px"
    @close="emit('close')"
  >
    <div class="h-[65vh]">
      <ElScrollbar>
        <div
          v-for="(item, index) in context"
          :key="index"
          class="py-[6px] px-[10px] border border-solid border-br-light rounded mb-[10px]"
        >
          <div class="font-medium text-tx-primary">
            {{ item.role }}
          </div>
          <div class="text-muted text-sm whitespace-pre-wrap">
            {{ item.content }}
          </div>
        </div>
      </ElScrollbar>
    </div>
  </ElDialog>
</template>
<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    context: any[]
  }>(),
  {
    context: () => []
  }
)

const emit = defineEmits(['close'])
</script>
