<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="是否显示">
                <el-radio-group v-model="isShowModel" class="ml-4">
                    <el-radio :label="true">显示</el-radio>
                    <el-radio :label="false">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="背景图片">
                <div>
                    <material-picker
                        v-model="propModel.bgImage"
                        upload-class="bg-body"
                        size="100px"
                        :exclude-domain="true"
                    />

                    <div class="form-tips">建议尺寸：1920px*645px</div>
                </div>
            </el-form-item>
            <el-form-item label="右侧图片">
                <div>
                    <material-picker
                        v-model="propModel.rightImage"
                        upload-class="bg-body"
                        size="100px"
                        :exclude-domain="true"
                    />

                    <div class="form-tips">建议尺寸：600px*460px</div>
                </div>
            </el-form-item>
            <el-form-item label="标题名称">
                <el-input v-model="propModel.title" />
            </el-form-item>
            <el-form-item label="简介描述">
                <el-input v-model="propModel.desc" type="textarea" :rows="10" resize="none" />
            </el-form-item>
            <el-form-item label="按钮显示">
                <el-radio-group v-model="propModel.isShowBtn">
                    <el-radio :label="true">显示</el-radio>
                    <el-radio :label="false">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="按钮文字">
                <el-input v-model="propModel.btnText" />
            </el-form-item>
            <el-form-item label="跳转链接">
                <link-picker v-model="propModel.link" />
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { Prop } from './config'

const props = defineProps<{
    isShow: boolean
    prop: Prop
}>()
const emit = defineEmits<{
    (event: 'update:prop', value: Prop): void
}>()
const propModel = useVModel(props, 'prop', emit)
const isShowModel = useVModel(props, 'isShow', emit)
</script>

<style lang="scss" scoped></style>
