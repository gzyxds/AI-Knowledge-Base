<template>
  <div class="inline-flex items-center">
    <el-input
      :model-value="current.name || keyModel"
      readonly
      placeholder="请选择配音"
      clearable
    >
      <template #suffix v-if="current.example">
        <div class="text-tx-primary flex cursor-pointer">
          <Icon
            v-if="audioPlaying"
            name="el-icon-VideoPause"
            :size="18"
            @click.stop="pause"
          />
          <Icon
            v-else
            name="el-icon-VideoPlay"
            :size="18"
            @click.stop="play(current.example)"
          />
        </div>
      </template>
    </el-input>

    <Popup
      title="选择配音"
      width="500px"
      @open="currentKey = keyModel"
      @confirm="keyModel = currentKey"
    >
      <template #trigger>
        <div class="ml-[20px]">
          <el-button type="primary" plain> 选择配音 </el-button>
        </div>
      </template>
      <div class="flex flex-wrap mx-[-10px]">
        <div
          class="w-[50%] mb-[15px] px-[10px]"
          v-for="(item, value) in dubList"
          :key="value"
        >
          <div class="w-full">
            <DubItem
              :is-active="value == currentKey"
              :value="item.name"
              :url="item.example"
              @click="currentKey = value"
            />
          </div>
        </div>
      </div>
    </Popup>
  </div>
</template>

<script setup lang="ts">
import { getDubbingList } from '@/api/digital'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
  modelValue: string
}>()
const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()
const currentKey = ref('')
const keyModel = useVModel(props, 'modelValue', emit)
const { play, pause, audioPlaying } = useAudioPlay()
const dubList = ref<Record<string, any>>({})
const getData = async () => {
  dubList.value = await getDubbingList()
}
const current = computed(() => {
  return dubList.value[keyModel.value] || {}
})
getData()
</script>
