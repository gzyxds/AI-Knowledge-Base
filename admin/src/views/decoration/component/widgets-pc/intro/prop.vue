<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="是否显示">
                <el-radio-group v-model="isShowModel" class="ml-4">
                    <el-radio :label="true">显示</el-radio>
                    <el-radio :label="false">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
            <div>
                <el-form-item label="导航菜单" >
                    <span class="form-tips !text-xs">建议尺寸：1168*675px</span>
                </el-form-item>
                <draggable
                    class="draggable"
                    v-model="propModel.data"
                    animation="300"
                    handle=".drag-move"
                >
                    <template v-slot:item="{ element: item, index }">
                        <del-wrap :key="index" @close="handleDelete(index)">
                            <div class="bg-fill-light w-full p-4 mb-4">
                                <el-form-item label="图片设置">
                                    <div>
                                        <material-picker
                                            v-model="item.image"
                                            upload-class="bg-body"
                                            size="100px"
                                            :exclude-domain="true"
                                        />

                                        <div class="form-tips">建议尺寸：1168px*675px</div>
                                    </div>
                                </el-form-item>
                                <el-form-item label="标题名称">
                                    <el-input v-model="item.title" />
                                </el-form-item>
                                <el-form-item label="副标题">
                                    <el-input
                                        v-model="item.subtitle"
                                        type="textarea"
                                        :rows="4"
                                        resize="none"
                                    />
                                </el-form-item>

                                <el-form-item label="跳转链接">
                                    <link-picker v-model="item.link" />
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

            <el-form-item label-width="0">
                <el-button type="primary" @click="handleAdd">添加功能</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { DataItem, Prop } from './config'
import Draggable from 'vuedraggable'
const props = defineProps<{
    isShow: boolean
    prop: Prop
}>()
const emit = defineEmits<{
    (event: 'update:prop', value: Prop): void
    (event: 'update:isShow', value: boolean): void
}>()
const propModel = useVModel(props, 'prop', emit)
const isShowModel = useVModel(props, 'isShow', emit)
const handleAdd = () => {
    propModel.value.data.push({
        image: '',
        title: '这是标题',
        subtitle: '这是一段标题简介',
        functionPoint: [],
        isShow: true
    })
}

const handleDelete = (index: number) => {
    propModel.value.data.splice(index, 1)
}
</script>

<style lang="scss" scoped></style>
