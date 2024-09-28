<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="是否显示">
                <el-radio-group v-model="isHiddenModel">
                    <el-radio :label="false">显示</el-radio>
                    <el-radio :label="true">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="菜单设置">
                <div class="flex-1">
                    <div class="form-tips">建议尺寸：80*80px</div>
                    <draggable
                        class="draggable"
                        v-model="contentModel.data"
                        animation="300"
                        handle=".drag-move"
                    >
                        <template v-slot:item="{ element: item, index }">
                            <del-wrap
                                :key="index"
                                @close="handleDelete(index)"
                                class="max-w-[400px]"
                            >
                                <div class="bg-fill-light w-full p-4 mt-4">
                                    <el-form-item label="功能图标">
                                        <div>
                                            <material-picker
                                                v-model="item.image"
                                                upload-class="bg-body"
                                                size="100px"
                                                :exclude-domain="true"
                                            />
                                        </div>
                                    </el-form-item>

                                    <el-form-item label="功能标题" class="mt-[18px]">
                                        <el-input
                                            v-model="item.title"
                                            placeholder="请输入功能标题"
                                        />
                                    </el-form-item>
                                    <el-form-item label="链接地址" class="mt-[18px]">
                                        <link-picker type="mobile" v-model="item.link" />
                                    </el-form-item>
                                    <el-form-item class="mt-[18px]" label="是否显示">
                                        <div class="flex-1 flex items-center">
                                            <el-switch
                                                :active-value="true"
                                                :inactive-value="false"
                                                v-model="item.isShow"
                                            />
                                            <div class="drag-move cursor-move ml-auto">
                                                <icon name="el-icon-Rank" size="18" />
                                            </div>
                                        </div>
                                    </el-form-item>
                                </div>
                            </del-wrap>
                        </template>
                    </draggable>
                </div>
            </el-form-item>
            <el-form-item>
                <el-button
                    :disabled="content.data?.length >= limit"
                    type="primary"
                    @click="handleAdd"
                >
                    添加功能
                </el-button>
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
