<template>
    <div class="flex justify-between select-none">
        <h3 class="flex items-center gap-2 font-bold mb-2">
            <p>
                <span>{{ title }}</span>
                <span v-if="required" class="text-error ml-1">*</span>
            </p>

            <el-popover
                v-if="tips !== 'none' && tips !== ''"
                placement="right"
                :width="200"
                trigger="hover"
                :show-arrow="false"
                transition="custom-popover"
                :content="tips"
            >
                <template #reference>
                    <div
                        class="flex items-center cursor-pointer text-[#999999]"
                    >
                        <Icon name="el-icon-QuestionFilled" :size="14" />
                    </div>
                </template>
            </el-popover>
        </h3>
        <div>
            <slot />
        </div>
    </div>
</template>

<script lang="ts" setup>
defineProps({
    title: {
        type: String,
        default: ''
    },
    tips: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    }
})
</script>

<style lang="scss" scoped></style>
