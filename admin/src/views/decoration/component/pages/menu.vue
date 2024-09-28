<template>
    <el-menu
        :default-active="modelValue"
        class="w-[220px] pages-menu border-none"
        @select="handleSelect"
    >
        <el-menu-item v-for="item in menus" :index="item.id" :key="item.id">
            <span>{{ item.name }}</span>
        </el-menu-item>
    </el-menu>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'

defineProps({
    menus: {
        type: Object as PropType<any[]>,
        default: () => ({})
    },
    modelValue: {
        type: Number,
        default: 1
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const handleSelect = (index: string) => {
    emit('update:modelValue', index)
}
</script>

<style lang="scss" scoped>
.pages-menu {
    height: calc(100vh - 180px);
    padding: 20px 0 20px 20px;

    :deep(.el-menu-item) {
        border-color: transparent;

        &.is-active {
            border-right-width: 2px;
            border-color: var(--el-color-primary);
            background-color: var(--el-color-primary-light-9);
        }
    }
}
</style>
