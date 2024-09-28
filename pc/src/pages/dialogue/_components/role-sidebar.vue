<template>
    <div
        class="bg-body rounded-[12px] flex flex-col h-full overflow-hidden text-tx-primary"
        style="width: var(--aside-panel-width)"
    >
        <div
            class="flex items-center justify-around text-xl font-medium px-[16px] pt-[16px] cursor-pointer"
        >
            <NuxtLink to="/dialogue/chat">
                <div class="pb-[8px]">问答助手</div>
            </NuxtLink>
            <div
                class="pb-[6px] text-primary border-solid border-b-[2px] border-primary"
            >
                角色助手
            </div>
        </div>
        <div class="px-[16px] pt-[16px] pb-[6px]">
            <el-input
                class="w-full leading-[32px] role-search"
                v-model="searchContent"
                :prefix-icon="Search"
                placeholder="请输入关键词搜索"
            />
        </div>
        <div class="flex-1 min-h-0">
            <ElScrollbar class="" ref="sidebarRef">
                <div class="px-[16px] pb-[16px]">
                    <DropDown
                        v-for="(item, index) in sidebarList"
                        :title="item.name"
                        :length="item.skill.length"
                        :key="index"
                    >
                        <template #menu>
                            <div
                                class="flex items-center mt-[15px] p-[10px] cursor-pointer rounded-[12px]"
                                :class="{
                                    'role-active': currentId == item1.id
                                }"
                                v-for="(item1, index1) in item.skill"
                                :key="index1"
                                @click="emit('ontoggle', item1)"
                            >
                                <ElImage
                                    :src="item1.image"
                                    class="w-[42px] h-[42px] rounded-[8px]"
                                ></ElImage>
                                <div class="ml-2 flex-1">
                                    <div class="text-base font-bold role-name">
                                        {{ item1.name }}
                                    </div>
                                    <div
                                        class="text-xs role-desc text-tx-placeholder line-clamp-1"
                                    >
                                        {{ item1.describe }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </DropDown>
                </div>
            </ElScrollbar>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ElScrollbar } from 'element-plus'
import { useVModel } from '@vueuse/core'
import { Search } from '@element-plus/icons-vue'

const emit = defineEmits(['ontoggle', 'update:keyword'])

const props = withDefaults(
    defineProps<{
        sidebarList: any[]
        currentId: number
        keyword: string
    }>(),
    {
        sidebarList: () => [],
        currentId: () => 0,
        keyword: ''
    }
)
const searchContent = useVModel(props, 'keyword', emit)

// 侧边滚动条ref
const sidebarRef = ref<InstanceType<typeof ElScrollbar>>()
</script>

<style lang="scss" scoped>
.dark {
    .role-search :deep(.el-input__wrapper) {
        --el-border-color: var(--el-bg-color);
        --el-input-border-color: var(--el-bg-color);
        background-color: #333333;
    }
    .role-active {
        background: #4a92ff;
    }
}

.role-search :deep(.el-input__wrapper) {
    --el-input-bg-color: #f7f7f7;
    --el-input-border-color: #f7f7f7;
}

.role-active {
    background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
    .role-name {
        @apply text-white;
    }
    .role-desc {
        @apply text-white;
    }
    @apply rounded-[12px];
}
</style>
