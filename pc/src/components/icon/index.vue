<template>
    <ElIcon
        v-if="name.includes(EL_ICON_PREFIX)"
        v-bind="{ ...props, ...$attrs }"
    >
        <component :is="name" />
    </ElIcon>
    <span v-if="name.includes(LOCAL_ICON_PREFIX)" class="local-icon">
        <SvgIcon v-bind="props" />
    </span>
</template>

<script lang="ts" setup>
import { ElIcon } from 'element-plus'
import SvgIcon from './svg-icon.vue'
import { EL_ICON_PREFIX, LOCAL_ICON_PREFIX } from '@/plugins/icons'
const props = defineProps({
    name: {
        type: String,
        default: ''
    },
    size: {
        type: [String, Number],
        default: '14px'
    },
    color: {
        type: String,
        default: 'inherit'
    }
})
</script>
