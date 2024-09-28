<template>
  <div class="pt-[10px]">
    <el-form-item label="启用形象" prop="is_digital">
      <div>
        <el-switch
          v-model="formData.is_digital"
          inline-prompt
          :active-value="1"
          :inactive-value="0"
          active-text="开启"
          inactive-text="关闭"
        />
      </div>
    </el-form-item>
    <template v-if="formData.is_digital">
      <!-- <el-form-item label="形象背景色" prop="digital_bg">
        <div>
          <ColorPicker v-model="formData.digital_bg" default-color="#FFFFFF" />
        </div>
      </el-form-item> -->
      <el-form-item label="选择形象" prop="digital_id">
        <ElButton type="primary" link @click="refresh">刷新</ElButton>
      </el-form-item>
      <div class="px-[16px]">
        <div class="flex flex-wrap">
          <!-- <ColorPicker v-model="formData.digital_bg" default-color="#FFFFFF" /> -->
          <NuxtLink
            to="/application/digital/edit"
            target="_blank"
            class="flex items-center justify-center p-[15px] border border-br-light border-solid w-[260px] rounded-[10px] h-[80px] mx-[7.5px] mb-[10px]"
          >
            <Icon name="el-icon-Plus" />
            新增形象
          </NuxtLink>
          <div
            class="flex items-center p-[15px] border border-br-light border-solid w-[260px] rounded-[10px] mx-[7.5px] cursor-pointer mb-[15px] h-[80px]"
            v-for="item in optionsData.digitalLists"
            :key="item.id"
            :class="{
              '!text-primary border-primary bg-primary-light-9':
                formData.digital_id == item.id
            }"
            @click="handelSelect(item.id)"
          >
            <ElImage
              class="w-[50px] h-[50px] rounded-[50%] overflow-hidden border border-solid border-white flex-none"
              fit="cover"
              :src="item.avatar"
            />
            <div class="line-clamp-2 ml-[15px]">{{ item.name }}</div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { getDigitalList } from '@/api/digital'
const props = defineProps<{
  modelValue: Record<string, any>
}>()
const emit = defineEmits<{
  (event: 'update:modelValue', value: Record<string, any>): void
}>()

const formData = useVModel(props, 'modelValue', emit)

const { optionsData, refresh } = useDictOptions<{
  digitalLists: any[]
}>({
  digitalLists: {
    api: getDigitalList,
    params: {
      page_type: 0
    },
    transformData(data) {
      return data.lists || []
    }
  }
})

const handelSelect = (id: number) => {
  if (formData.value.digital_id == id) {
    formData.value.digital_id = ''
    return
  }
  formData.value.digital_id = id
}
</script>
