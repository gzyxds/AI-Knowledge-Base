<template>
    <div>
        <el-form label-width="70px" class="max-w-[400px]">
            <el-form-item label="是否显示">
                <el-radio-group v-model="isHiddenModel">
                    <el-radio :label="false">显示</el-radio>
                    <el-radio :label="true">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="显示样式">
                <el-radio-group v-model="content.showType">
                    <el-radio :label="1">列表模式</el-radio>
                    <el-radio :label="2">一行2个</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="标题名称">
                <el-input v-model="contentModel.title" placeholder="请输入标题名称" />
            </el-form-item>
            <el-form-item label="选择模型">
                <el-radio-group v-model="contentModel.dataType">
                    <el-radio :label="1">系统推荐</el-radio>
                    <el-radio :label="2">手动选择</el-radio>
                </el-radio-group>
                <div v-if="contentModel.dataType === 1">
                    <div class="bg-primary-light-9 text-primary px-2 rounded-sm mt-2 text-xs">
                        系统推荐规则：根据使用人数>排序值>添加时间来显示
                    </div>
                </div>
            </el-form-item>
            <el-form-item label="显示数量" v-if="contentModel.dataType == 1">
                <el-slider v-model="contentModel.dataNum" :min="1" :max="50" show-input />
            </el-form-item>
            <el-form-item v-else>
                <creative-picker v-model="contentModel.data" />
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { useVModel } from '@vueuse/core'
import type options from './options'
import feedback from '@/utils/feedback'
import Draggable from 'vuedraggable'
type OptionsType = ReturnType<typeof options>
const props = defineProps<{
    isHidden: boolean
    content: OptionsType['content']
}>()
const emit = defineEmits<{
    (event: 'update:isHidden', value: boolean): void
}>()

const isHiddenModel = useVModel(props, 'isHidden', emit)

const contentModel = useVModel(props, 'content', emit)
const limit = 999
const handleAdd = () => {
    if (contentModel.value.data?.length < limit) {
        contentModel.value.data.push({
            image: '',
            title: '',
            link: {},
            isShow: true
        })
    } else {
        feedback.msgError(`最多添加${limit}个`)
    }
}
const handleDelete = (index: number) => {
    if (contentModel.value.data?.length <= 1) {
        return feedback.msgError('最少保留一个')
    }
    contentModel.value.data.splice(index, 1)
}
</script>

<style lang="scss" scoped></style>
