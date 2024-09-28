<template>
    <div class="h-full flex">
        <InfoMenu
            v-model="defaultTab"
            :menu-list="tabList"
            back-path="/application/layout/robot"
            @update:model-value="handleSelect"
        >
            <template #title>
                <div>
                    <el-popover
                        placement="bottom"
                        :width="180"
                        trigger="click"
                        :show-arrow="false"
                        transition="custom-popover"
                        v-model:visible="showAppLists"
                    >
                        <div>
                            <ElScrollbar style="height: 250px">
                                <div
                                    class="flex items-center leading-10 cursor-pointer hover:bg-primary-light-9 px-[10px] my-[5px] rounded-[12px] hover:text-primary"
                                    v-for="item in robotStore.robotLists"
                                    :key="item.id"
                                    @click="changeApp(item.id)"
                                >
                                    <img
                                        class="rounded-[50%] w-[28px] h-[28px] flex-none"
                                        :src="item.image"
                                        alt=""
                                    />
                                    <div class="ml-[8px] line-clamp-1">
                                        {{ item.name }}
                                    </div>
                                </div>
                            </ElScrollbar>
                        </div>

                        <template #reference>
                            <div class="flex items-center cursor-pointer">
                                <div class="text-xl flex-1 min-w-0">
                                    <OverflowTooltip
                                        :content="appInfo.name"
                                        :teleported="true"
                                        effect="light"
                                    />
                                </div>
                                <Icon name="el-icon-ArrowDown" />
                            </div>
                        </template>
                    </el-popover>
                </div>
            </template>
        </InfoMenu>

        <div
            class="sm:h-full py-[16px] pr-[16px] flex flex-col sm:flex-row flex-1 min-w-0"
        >
            <div class="sm:h-full flex-1 min-w-0 min-h-0 bg-body rounded-[12px]">
                <AppEdit
                    v-if="defaultTab === 'edit'"
                    :model-value="appInfo"
                    @success="router.push('/application/layout/robot')"
                />
                <ElScrollbar v-if="defaultTab === 'release'">
                    <AppRelease :app-id="appId" />
                </ElScrollbar>
                <AppDialogue v-if="defaultTab === 'dialogue'" :app-id="appId" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppEdit from './_components/app-edit/index.vue'
import AppRelease from './_components/app-release/index.vue'
import AppDialogue from './_components/app-dialogue/index.vue'
import { getRobotDetail } from '@/api/robot'
import { useRobotStore } from '@/stores/robot'
import { useAppStore } from '@/stores/app'
const route = useRoute()
const router = useRouter()
const appStore = useAppStore()
const robotStore = useRobotStore()
robotStore.getRobot()
const showAppLists = ref(false)
const appId = ref(route.query.id as string)
const { data: appInfo, refresh } = await useAsyncData(
    () =>
        getRobotDetail({
            id: appId.value
        }),
    {
        transform(data) {
            if (data?.category_id === 0) {
                data.category_id = ''
            }
            return data
        },
        default() {
            return {}
        },
        lazy: true
    }
)
const defaultTab = ref('edit')

definePageMeta({
    auth: true,
    activePath: '/application/layout/robot',
    hiddenFooter: true,
    title: '智能体设置'
})
const tabList = [
    {
        name: '智能体设置',
        icon: 'el-icon-Setting',
        key: 'edit'
    },
    {
        name: '发布智能体',
        key: 'release',
        icon: 'el-icon-Position'
    },
    {
        name: '对话数据',
        key: 'dialogue',
        icon: 'el-icon-ChatDotRound'
    },
    {
        name: '立即对话',
        key: 'chat',
        icon: 'el-icon-ChatLineRound'
    }
]

const handleSelect = (key: string) => {
    switch (key) {
        case 'chat':
            router.push({
                path: '/application/chat',
                query: {
                    id: appId.value
                }
            })
            break
        default:
            router.replace({
                path: route.path,
                query: {
                    ...route.query,
                    currentTab: key
                }
            })
    }
}

const changeApp = async (id: string) => {
    showAppLists.value = false
    if (id == route.query.id) return
    appId.value = id
    await refresh()
    router.replace({
        path: route.path,
        query: {
            ...route.query,
            id
        }
    })
}

watch(
    () => route.query,
    (query) => {
        defaultTab.value = (query.currentTab as string) || 'edit'
    },
    {
        immediate: true
    }
)
</script>

<style lang="scss" scoped>
.setting-aside {
}
.tab-lists {
    :deep() {
        .el-menu {
            --el-menu-item-height: 38px;
            --el-menu-base-level-padding: 15px;
            border-right: none;
            --el-menu-bg-color: transparent;
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
                border-radius: 6px;
                &:hover {
                    @apply text-primary;
                }
                &.is-active {
                    background: linear-gradient(
                        90deg,
                        #70c3ec 0%,
                        #4A92FF 100%
                    );
                    @apply text-white;
                }
            }
        }
    }
}
</style>
