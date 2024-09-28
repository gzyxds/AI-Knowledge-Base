<template>
    <div class="mt-[15px]">
        <sidbar-item-title title="图片质量" tips="" required />

        <div class="mt-[10px]">
            <div
                v-for="item in typeList"
                :key="item.value"
                class="picture-quality-option rounded-[12px]"
                :class="{
                    'picture-quality-option__active': item.value === value
                }"
                @click="value = item.value"
            >
                {{ item.label }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import sidbarItemTitle from './../common/sidbar-item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: {
            version: '',
            style: 'standard'
        }
    }
)
const { modelValue: value } = useVModels(props, emit)

const typeList: any = [
    {
        value: 'standard',
        label: '标准'
    },
    {
        value: 'hd',
        label: 'HD-高清'
    }
]

value.value = 'standard'
</script>

<style lang="scss" scoped>
.picture-quality-option:nth-child(2n) {
    margin-left: 10px;
}

.picture-quality-option {
    display: inline-block;
    cursor: pointer;
    width: 155px;
    padding: 8px 0;
    text-align: center;
    border: 1px solid transparent;
    @apply bg-page;
}

.picture-quality-option:hover,
.picture-quality-option__active {
    color: var(--el-color-primary);
    border: 1px solid var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);
}
</style>