<template>
    <div class="pt-[10px]">
        <el-form-item label="启用形象" prop="is_digital">
            <div>
                <el-switch
                    v-model="formData.is_digital"
                    inline-prompt
                    :active-value="1"
                    :inactive-value="0"
                />
            </div>
        </el-form-item>
        <template v-if="formData.is_digital">
            <el-form-item label="选择形象" prop="digital_id">
                <div>
                    <div class="flex flex-wrap">
                        <div
                            class="flex items-center p-[15px] border border-br-light border-solid w-[260px] rounded-[10px] cursor-pointer h-[80px]"
                            :class="{
                                '!text-primary border-primary': true
                            }"
                        >
                            <ElImage
                                class="w-[50px] h-[50px] rounded-[50%] overflow-hidden border border-solid border-white flex-none"
                                fit="cover"
                                :src="formData.digital?.avatar"
                            />
                            <div class="line-clamp-2 ml-[15px]">{{ formData.digital?.name }}</div>
                        </div>
                    </div>
                </div>
            </el-form-item>
        </template>
    </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
const props = defineProps<{
    modelValue: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
}>()

const formData = useVModel(props, 'modelValue', emit)
</script>
