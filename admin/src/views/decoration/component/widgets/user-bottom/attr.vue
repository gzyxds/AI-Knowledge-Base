<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="标题名称">
                <el-input class="w-[400px]" v-model="content.data.title" />
            </el-form-item>
            <el-form-item label="内容文案">
                <el-input
                    class="w-[400px]"
                    v-model="content.data.content"
                    type="textarea"
                    rows="3"
                />
            </el-form-item>
            <el-form-item label="内容文案">
                <div>
                    <el-switch
                        :active-value="1"
                        :inactive-value="0"
                        v-model="content.data.canCopy"
                    />
                    <div class="form-tips">开启的话，支持点击复制内容</div>
                </div>
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
