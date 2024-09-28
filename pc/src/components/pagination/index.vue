<template>
    <div class="pagination">
        <el-pagination
            v-bind="props"
            :background="background"
            v-model:currentPage="pager.page"
            v-model:pageSize="pager.size"
            :pager-count="5"
            :page-sizes="pageSizes"
            :layout="layout"
            :total="pager.count"
            :hide-on-single-page="hideOnSinglePage"
            @size-change="sizeChange"
            @current-change="pageChange" />
    </div>
</template>

<script lang="ts" setup>
interface Props {
    modelValue?: Record<string, any>
    pageSizes?: number[]
    layout?: string
    hideOnSinglePage: boolean
    background: boolean
}
const props = withDefaults(defineProps<Props>(), {
    modelValue: () => ({}),
    pageSizes: () => [15, 20, 30, 40],
    layout: 'total, sizes, prev, pager, next, jumper',
    hideOnSinglePage: false,
    background: false
})

const emit = defineEmits<{
    (event: 'change'): void
    (event: 'update:modelValue', value: any): void
}>()

const pager = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})
const sizeChange = () => {
    pager.value.page = 1
    emit('change')
}
const pageChange = () => {
    emit('change')
}
</script>
