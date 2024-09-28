<template>
    <div class="flex flex-wrap mx-[-7px] mb-[-14px]">
        <div
            class="flex max-w-full items-center mx-[7px] cursor-pointer hover:bg-fill-light mb-[14px] px-[15px] py-[10px] border border-br-light border-solid rounded-[12px]"
            v-for="(item, index) in lists"
            :key="index"
            @click="emit('click-item', getText(item))"
        >
            <div class="flex-1 line-clamp-1 text-tx-secondary">
                {{ getText(item) }}
            </div>
            <span class="text-primary flex ml-[10px]">
                <Icon name="el-icon-Right" />
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        lists: any[]
        prop: string
    }>(),
    {
        lists: () => [],
        prop: ''
    }
)
const emit = defineEmits<{
    'click-item': [value: string]
}>()

const getText = (item: any) => {
    return item[props.prop] ? item[props.prop] : item
}
</script>
