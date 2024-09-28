<template>
  <div>
    <DropdownMore class="mt-[-5px]" :default-height="42">
      <el-radio-group v-model="state.type" class="el-radio-group-margin">
        <el-radio-button
          v-for="item in effectCategory"
          :key="item.type"
          :label="item.type"
        >
          {{ item.name }}
        </el-radio-button>
      </el-radio-group>
    </DropdownMore>
    <div v-if="effectList.length" class="flex flex-wrap mx-[-7px] mt-[10px]">
      <div class="w-[33.3%]">
        <div class="px-[7px] mb-[14px]">
          <div
            class="cursor-pointer overflow-hidden border border-solid border-br-light rounded-lg p-[5px]"
            @click="emit('update:modelValue', {})"
          >
            <div class="rounded-lg overflow-hidden bg-[black]">
              <div class="pic-wrap h-0 pt-[75%] relative text-white">
                <div class="absolute inset-0 flex items-center justify-center">
                  <Icon name="local-icon-cancel" :size="25" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-for="item in effectList" :key="item.server_key" class="w-[33.3%]">
        <div class="px-[7px] mb-[14px]">
          <div
            class="cursor-pointer overflow-hidden border border-solid border-br-light rounded-lg p-[5px]"
            @click="emit('update:modelValue', cloneDeep(item))"
            :class="{
              'border-primary': item.server_key === modelValue.server_key
            }"
          >
            <div class="rounded-lg overflow-hidden">
              <div class="pic-wrap h-0 pt-[75%] relative">
                <div class="absolute inset-0">
                  <ElImage
                    :src="item.url"
                    class="w-full h-full"
                    fit="contain"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ElEmpty :image="empty_con" v-else description="暂无数据～" />
  </div>
</template>
<script lang="ts" setup>
import { getEffectList } from '@/api/digital_human'
import empty_con from '@/assets/image/empty_con.png'
import { useQuery } from '@tanstack/vue-query'
import { cloneDeep } from 'lodash-es'
const props = defineProps<{
  modelValue: Record<string, any>
}>()
const emit = defineEmits<{
  (event: 'update:modelValue', value: Record<string, any>): void
}>()
const state = reactive({
  type: 'in'
})

const { data: effectCategory, suspense } = useQuery(['effectList'], {
  queryFn: getEffectList
})
await suspense()
const effectList = computed(() => {
  return (
    effectCategory.value.find((item: any) => item.type === state.type)?.list ||
    []
  )
})

watch(
  () => props.modelValue.type,
  (value) => {
    state.type = value || 'in'
  },
  {
    immediate: true
  }
)
</script>

<style lang="scss" scoped>
.avatar-select {
  :deep() {
    .el-upload {
      display: flex;
    }
  }
}
</style>
