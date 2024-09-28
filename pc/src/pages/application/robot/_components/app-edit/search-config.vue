<template>
    <div class="pt-[10px]">
        <el-form-item label="AI模型" prop="model_id">
            <div class="w-80">
                <ModelPicker
                    class="flex-1"
                    v-model:id="formData.model_id"
                    v-model:sub_id="formData.model_sub_id"
                    :set-default="false"
                    disabled
                />
            </div>
        </el-form-item>

        <el-form-item label="相似度" required prop="search_similarity">
            <div class="flex-1 min-w-0">
                <div class="w-full flex">
                    <div class="flex-1 max-w-[320px]">
                        <el-slider
                            :min="0"
                            :max="1"
                            :step="0.001"
                            v-model="formData.search_similarity"
                        />
                    </div>
                </div>

                <div class="form-tips">
                    输入0-1之间的数值，支持3位小数点；高相似度推荐设置0.8以上
                </div>
            </div>
        </el-form-item>
        <el-form-item label="单次搜索数量" required prop="search_limits">
            <div class="flex-1 min-w-0">
                <div class="w-full flex">
                    <div class="flex-1 max-w-[320px]">
                        <el-slider :min="0" :max="20" v-model="formData.search_limits"/>
                    </div>
                </div>

                <div class="form-tips">默认设置为5，请输入0-20之间的整数数值</div>
            </div>
        </el-form-item>

        <el-form-item label="空搜索回复">
            <el-radio-group v-model="formData.search_empty_type">
                <el-radio :label="1"> AI回复</el-radio>
                <el-radio :label="2"> 自定义回复</el-radio>
            </el-radio-group>
        </el-form-item>
        <el-form-item v-if="formData.search_empty_type === 2">
            <div class="w-80">
                <el-input
                    v-model="formData.search_empty_text"
                    placeholder="请输入回复内容，当搜索匹配不上内容时，直接回复填写的内容"
                    type="textarea"
                    :autosize="{ minRows: 6, maxRows: 6 }"
                    :maxlength="1000"
                    show-word-limit
                    clearable
                />
            </div>
        </el-form-item>
    </div>
</template>

<script setup lang="ts">
import {useVModel} from '@vueuse/core'
import {useAppStore} from "~/stores/app";

const props = defineProps<{
    modelValue: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
}>()
const formData = useVModel(props, 'modelValue', emit)

const appStore = useAppStore()
</script>
