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
                <el-form-item label="显示样式">
                    <el-radio-group v-model="propModel.showType" class="ml-4">
                        <el-radio :label="3">一行3个</el-radio>
                        <el-radio :label="4">一行4个</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="导航菜单" >
                    <span class="form-tips !text-xs">建议尺寸：20*20px</span>
                </el-form-item>
                <draggable
                    class="draggable"
                    v-model="propModel.data"
                    animation="300"
                    draggable=".draggable"
                    handle=".drag-move"
                >
                    <template v-slot:item="{ element: item, index }">
                        <del-wrap :key="index" @close="handleDelete(index)">
                            <div class="bg-fill-light w-full p-4 mb-4">
                                <el-form-item label="导航图标">
                                    <material-picker
                                        v-model="item.icon"
                                        upload-class="bg-body"
                                        size="60px"
                                        :exclude-domain="true"
                                    />
                                </el-form-item>
                                <el-form-item label="场景名称">
                                    <el-input v-model="item.title" />
                                </el-form-item>
                                <el-form-item label="场景说明">
                                    <el-input
                                        v-model="item.desc"
                                        type="textarea"
                                        :rows="4"
                                        resize="none"
                                    />
                                </el-form-item>
                                <el-form-item label="跳转链接">
                                    <div class="w-full">
                                        <link-picker v-model="item.link" />
                                    </div>
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
                <el-button type="primary" @click="handleAdd">添加应用</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { Prop } from './config'
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
        icon: '',
        title: '',
        desc: '',
        isShow: true
    })
}
const handleDelete = (index: number) => {
    propModel.value.data.splice(index, 1)
}
</script>

<style lang="scss" scoped></style>
