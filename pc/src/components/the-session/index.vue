<template>
    <div class="session bg-body rounded-[12px] flex flex-col">
        <slot name="top"></slot>
        <div class="p-[16px]">
            <ElButton
                type="primary"
                class="w-full session-add-btn"
                size="large"
                @click="emit('add')"
            >
                + 新建会话
            </ElButton>
        </div>
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="px-[16px]">
                    <div v-for="item in data" :key="item.id">
                        <SessionItem
                            v-model="activeId"
                            :item-id="item.id"
                            :name="item.name"
                            @delete="emit('delete', item.id)"
                            @edit="handleEdit($event, item.id)"
                        />
                    </div>
                </div>
            </ElScrollbar>
        </div>
        <div class="p-[16px]">
            <ElButton
                class="w-full"
                plain
                size="large"
                @click="emit('clear')"
            >
                <template #icon>
                    <Icon name="el-icon-Delete" />
                </template>
                清除所有会话
            </ElButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import SessionItem from './item.vue'

const props = withDefaults(
    defineProps<{
        data: any[]
        modelValue: string | number
    }>(),
    {}
)
const emit = defineEmits<{
    (event: 'add'): void
    (event: 'clear'): void
    (event: 'edit', value: { name: string; id: number }): void
    (event: 'delete', id: number): void
    (event: 'update:modelValue', value: string | number): void
}>()

const activeId = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

const handleEdit = (name: string, id: number) => {
    emit('edit', {
        name,
        id
    })
}
</script>

<style lang="scss" scoped>
.session {
    // background-color: var(--aside-bg-color);
    width: var(--aside-panel-width);
    height: 100%;

    &-add-btn {
        border: none;
    }
}
</style>