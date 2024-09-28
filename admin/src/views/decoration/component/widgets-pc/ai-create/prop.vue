<template>
    <div>
        <el-form label-width="70px">

            <el-form-item label="背景图片">
                <div>
                    <material-picker
                        v-model="propModel.banner_bg"
                        upload-class="bg-body"
                        exclude-domain
                    />
                    <div class="form-tips">
                        建议图片尺寸为：1920px*300px
                    </div>
                </div>
            </el-form-item>
            <el-form-item label="标题名称">
                <el-input v-model="propModel.title" />
            </el-form-item>
            <el-form-item label="按钮显示">
                <el-radio-group v-model="propModel.title_color">
                    <el-radio :label="1">黑色</el-radio>
                    <el-radio :label="2">白色</el-radio>
                    <el-radio :label="3">主题色</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { Prop } from './config'

const props = defineProps<{
    prop: Prop
}>()
const emit = defineEmits<{
    (event: 'update:prop', value: Prop): void
}>()
const propModel = useVModel(props, 'prop', emit)
</script>

<style lang="scss" scoped></style>
