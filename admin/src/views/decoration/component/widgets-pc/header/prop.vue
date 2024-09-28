<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="导航菜单">
                <span class="form-tips !text-xs">建议尺寸：20*20px</span>
            </el-form-item>
            <div>
                <draggable
                    class="draggable"
                    v-model="propModel.nav"
                    animation="300"
                    handle=".drag-move"
                >
                    <template v-slot:item="{ element, index }">
                        <del-wrap @close="handleDelete(index)" class="max-w-[400px]">
                            <div class="bg-fill-light w-full p-4 mb-4">
                                <el-form-item label="导航名称">
                                    <el-input v-model="element.name" placeholder="请输入名称" />
                                </el-form-item>
                                <el-form-item label="链接地址">
                                    <link-picker type="pc" :is-tab="true" v-model="element.link" />
                                </el-form-item>
                                <el-form-item label="是否显示">
                                    <div class="flex-1 flex items-center">
                                        <el-switch
                                            :active-value="true"
                                            :inactive-value="false"
                                            v-model="element.isShow"
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
                <el-button type="primary" @click="handleAdd"> 添加导航 </el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import Draggable from 'vuedraggable'
import type { Prop } from './config'
import feedback from '@/utils/feedback'

const props = defineProps<{
    prop: Prop
}>()
const emit = defineEmits<{
    (event: 'update:prop', value: Prop): void
}>()
const propModel = useVModel(props, 'prop', emit)

const max = 10
const min = 2

const handleAdd = () => {
    if (propModel.value.nav?.length < max) {
        propModel.value.nav.push({
            name: '',
            selected: '',
            unselected: '',
            isShow: true,
            link: {}
        })
    } else {
        feedback.msgError(`最多添加${max}个`)
    }
}
const handleDelete = (index: number) => {
    if (propModel.value.nav?.length <= min) {
        return feedback.msgError(`最少保留${min}个`)
    }
    propModel.value.nav.splice(index, 1)
}
</script>

<style lang="scss" scoped></style>
