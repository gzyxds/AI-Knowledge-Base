<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="是否显示">
                <el-radio-group v-model="isShowModel" class="ml-4">
                    <el-radio :label="true">显示</el-radio>
                    <el-radio :label="false">不显示</el-radio>
                </el-radio-group>
            </el-form-item>

            <el-tabs v-model="activeName">
                <el-tab-pane label="背景设置" name="bg">
                    <div>
                        <div class="bg-fill-light w-full p-4 mb-4">
                            <el-form-item label="背景图片">
                                <div>
                                    <material-picker
                                        v-model="propModel.bgImage"
                                        upload-class="bg-body"
                                        size="100px"
                                        :exclude-domain="true"
                                    />

                                    <div class="form-tips">建议尺寸：1920px*347px</div>
                                </div>
                            </el-form-item>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane label="左侧设置" name="left">
                    <div>
                        <div class="bg-fill-light w-full p-4 mb-4">
                            <el-form-item label="logo图片">
                                <div>
                                    <material-picker
                                        v-model="propModel.logoImage"
                                        upload-class="bg-body"
                                        size="100px"
                                        :exclude-domain="true"
                                    />

                                    <div class="form-tips">建议尺寸：240px*34px</div>
                                </div>
                            </el-form-item>
                            <el-form-item label="广告语">
                                <el-input v-model="propModel.content" />
                            </el-form-item>
                            <el-form-item label="是否显示">
                                <el-switch v-model="propModel.isShowLeft" :active-value="1" :inactive-value="0"></el-switch>
                            </el-form-item>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane label="中间菜单" name="middle">
                    <el-form-item label="栏目一">
                        <el-input v-model="propModel.column1" />
                    </el-form-item>
                    <div>
                        <div class="el-form-item__label w-[70px] mb-[10px]">菜单设置</div>
                        <draggable
                            class="draggable px-[10px]"
                            v-model="propModel.columnMenu1"
                            animation="300"
                            handle=".drag-move"
                        >
                            <template v-slot:item="{ element: item, index }">
                                <del-wrap :key="index" @close="handleDeleteColumn1(index)">
                                    <div class="bg-fill-light w-full p-4 mb-4">
                                        <el-form-item label="菜单名称">
                                            <el-input v-model="item.title" />
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
                        <el-form-item label-width="0">
                            <el-button type="primary" @click="handleAddColumn1">添加菜单</el-button>
                        </el-form-item>
                    </div>
                    <el-form-item label="栏目二">
                        <el-input v-model="propModel.column2" />
                    </el-form-item>
                    <div>
                        <div class="el-form-item__label w-[70px] mb-[10px]">菜单设置</div>
                        <draggable
                            class="draggable px-[10px]"
                            v-model="propModel.columnMenu2"
                            animation="300"
                            handle=".drag-move"
                        >
                            <template v-slot:item="{ element: item, index }">
                                <del-wrap :key="index" @close="handleDeleteColumn2(index)">
                                    <div class="bg-fill-light w-full p-4 mb-4">
                                        <el-form-item label="菜单名称">
                                            <el-input v-model="item.title" />
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
                        <el-form-item label-width="0">
                            <el-button type="primary" @click="handleAddColumn2">添加菜单</el-button>
                        </el-form-item>
                    </div>
                </el-tab-pane>
                <el-tab-pane label="右侧二维码" name="right">
                    <div>
                        <div class="bg-fill-light w-full p-4 mb-4">
                            <el-form-item label="二维码">
                                <div>
                                    <material-picker
                                        v-model="propModel.rightQrcode1"
                                        upload-class="bg-body"
                                        size="100px"
                                        :exclude-domain="true"
                                    />

                                    <div class="form-tips">建议尺寸：120px*120px</div>
                                </div>
                            </el-form-item>
                            <el-form-item label="标题名称">
                                <el-input v-model="propModel.rightQrcodeTitle1" />
                            </el-form-item>
                            <el-form-item label="是否显示">
                                <el-switch v-model="propModel.rightQrcodeShow1" :active-value="1" :inactive-value="0"></el-switch>
                            </el-form-item>
                        </div>
                        <div class="bg-fill-light w-full p-4 mb-4">
                            <el-form-item label="二维码">
                                <div>
                                    <material-picker
                                        v-model="propModel.rightQrcode2"
                                        upload-class="bg-body"
                                        size="100px"
                                        :exclude-domain="true"
                                    />

                                    <div class="form-tips">建议尺寸：120px*120px</div>
                                </div>
                            </el-form-item>
                            <el-form-item label="标题名称">
                                <el-input v-model="propModel.rightQrcodeTitle2" />
                            </el-form-item>
                            <el-form-item label="是否显示">
                                <el-switch v-model="propModel.rightQrcodeShow2" :active-value="1" :inactive-value="0"></el-switch>
                            </el-form-item>
                        </div>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { Prop } from './config'
import Draggable from "vuedraggable";
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

const activeName = ref('bg')

const handleAddColumn1 = () => {
    propModel.value.columnMenu1.push({
        title: '',
        isShow: true
    })
}

const handleDeleteColumn1 = (index: number) => {
    propModel.value.columnMenu1.splice(index, 1)
}

const handleAddColumn2 = () => {
    propModel.value.columnMenu2.push({
        title: '',
        isShow: true
    })
}

const handleDeleteColumn2 = (index: number) => {
    propModel.value.columnMenu2.splice(index, 1)
}
</script>

<style lang="scss" scoped></style>
