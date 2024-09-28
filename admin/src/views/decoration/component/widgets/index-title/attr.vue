<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="是否显示">
                <el-radio-group v-model="isHiddenModel">
                    <el-radio :label="false">显示</el-radio>
                    <el-radio :label="true">不显示</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { useVModel } from '@vueuse/core'
import type options from './options'
type OptionsType = ReturnType<typeof options>
const props = defineProps<{
    isHidden: boolean
    content: OptionsType['content']
}>()
const emit = defineEmits<{
    (event: 'update:isHidden', value: boolean): void
}>()

const isHiddenModel = useVModel(props, 'isHidden', emit)
</script>

<style lang="scss" scoped></style>
