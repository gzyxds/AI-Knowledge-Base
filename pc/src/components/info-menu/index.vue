<template>
    <div class="setting-aside p-4">
        <div class="flex flex-col h-full bg-body w-[180px] rounded-[12px]">
            <div class="px-[15px] pt-[15px]">
                <div class="flex items-center cursor-pointer">
                    <div
                        class="flex bg-body p-[5px] text-bold rounded-[50%] text-primary shadow-light"
                        @click="back"
                    >
                        <Icon name="el-icon-Back" :size="18" />
                    </div>
                    <div class="text-xl flex-1 min-w-0 ml-[10px]">
                        <slot name="title">
                            <OverflowTooltip
                                v-if="title"
                                :content="title"
                                :teleported="true"
                                effect="light"
                            />
                        </slot>
                    </div>
                </div>
            </div>

            <ElScrollbar class="tab-lists w-full">
                <div class="mb-[10px]">
                    <el-menu
                        :default-active="currentTab"
                        style="border: none"
                        :router="false"
                        @select="($event) => (currentTab = $event)"
                    >
                        <el-menu-item
                            v-for="item in menuList"
                            :key="item.key"
                            :index="item.key"
                        >
                            <Icon v-if="item.icon" :name="item.icon" />
                            <span>{{ item.name }}</span>
                        </el-menu-item>
                    </el-menu>
                </div>
            </ElScrollbar>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useVModel } from '@vueuse/core'

const props = withDefaults(
    defineProps<{
        modelValue: string
        menuList: Array<{ name: string; icon: string; key: string }>
        backPath?: string
        title?: string
    }>(),
    {
        modelValue: '',
        menuList: () => [],
        title: ''
    }
)

const emit = defineEmits<{
    'update:modelValue': [value: string]
}>()
const router = useRouter()
const currentTab = useVModel(props, 'modelValue')

const back = () => {
    if (props.backPath) {
        router.replace({
            path: props.backPath
        })
    } else {
        router.back()
    }
}
</script>
<style lang="scss" scoped>
.tab-lists {
    :deep() {
        .el-menu {
            --el-menu-item-height: 38px;
            --el-menu-base-level-padding: 15px;
            border-right: none;
            --el-menu-bg-color: transparent;
            --el-menu-text-color: var(--el-text-color-primary);

            &--vertical {
                .el-menu-item {
                    margin: var(--el-menu-base-level-padding);
                    padding: 0 16px;
                    @apply bg-body;
                }
            }

            &--horizontal {
                & > .el-menu-item {
                    border-bottom: none;
                    padding: 0 10px;

                    &.is-active {
                        border-bottom: none;
                    }
                }
            }

            .el-menu-item {
                border-radius: 12px;

                &:hover {
                    @apply text-primary;
                }

                &.is-active {
                    @apply text-white bg-primary;
                }
            }
        }
    }
}
</style>