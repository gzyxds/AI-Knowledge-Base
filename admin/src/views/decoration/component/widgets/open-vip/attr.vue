<template>
    <div>
        <el-form label-width="80px">
            <el-form-item label="会员图标" class="is-required">
                <div>
                    <material-picker v-model="content.icon" exclude-domain />
                    <div class="form-tips">建议尺寸：60*60</div>
                </div>
            </el-form-item>
            <el-form-item label="背景图" class="is-required">
                <div>
                    <material-picker v-model="content.bg" exclude-domain />
                    <div class="form-tips">建议尺寸：710*112</div>
                </div>
            </el-form-item>
            <el-form-item label="标题名称" class="is-required">
                <el-input class="w-[360px]" v-model="content.title"></el-input>
            </el-form-item>
            <el-form-item label="副标题名" class="is-required">
                <el-input class="w-[360px]" v-model="content.sub_title"></el-input>
            </el-form-item>
            <el-form-item label="按钮文案" class="is-required">
                <el-input class="w-[360px]" v-model="content.btn"></el-input>
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped></style>
