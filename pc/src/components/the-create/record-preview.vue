<template>
  <el-drawer
    ref="drawerRef"
    v-model="visible"
    title="创作详情"
    direction="rtl"
    class="record-preview-drawer"
    size="50%"
    append-to-body
  >
    <div class="h-full flex flex-col">
      <div class="flex items-center pb-[16px]">
        <div class="text-lg flex-1 min-w-0 font-medium">
          <overflow-tooltip :content="title" />
        </div>
        <el-button link @click="emit('copy', content)">
          <Icon name="el-icon-DocumentCopy" :size="18" />
          <span class="ml-[10px]">复制</span>
        </el-button>
      </div>

      <div class="flex-1 min-h-0">
        <ElScrollbar>
          <div class="pt-[16px]">
            <Markdown :content="content" />
          </div>
        </ElScrollbar>
      </div>
    </div>
  </el-drawer>
</template>
<script lang="ts" setup>
import { useAppStore } from '~/stores/app'

const appStore = useAppStore()
const props = withDefaults(
  defineProps<{
    title?: string
    time?: string
    content?: string
    overflow?: boolean
  }>(),
  {
    title: '',
    time: '',
    content: '',
    overflow: false
  }
)
const emit = defineEmits<{
  (event: 'copy', text: string): void
}>()
const visible = ref(false)
const open = () => {
  visible.value = true
}

defineExpose({
  open
})
</script>
<style lang="scss">
.record-preview-drawer {
  min-width: 375px;
}
</style>
