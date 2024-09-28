<template>
    <NuxtLayout name="default">
        <div class="h-full flex flex-col 4xl:w-[2000px] mx-auto">
            <header
                class="robot-square-header flex flex-col justify-center items-center px-[16px] m-[16px] rounded-[12px] overflow-hidden"
                :style="{
                    'background-image': `url(${appStore.getImageUrl(
                      pages?.[0]?.prop?.banner_bg
                    )})`
                }"
            >
                <div
                    class="font-medium 2xl:text-[50px] xl:text-[40px] lg:text-[36px] text-[36px]"
                    :class="getTitleColor(pages?.[0]?.prop?.title_color)"
                >
                    {{ pages?.[0]?.prop?.title }}
                </div>

                <div class="tabs-list grid grid-cols-4 gap-4 mt-4">
                    <div
                        v-for="(item, index) in tabList"
                        class="tabs-item bg-white"
                        :class="{
                            'is-active': tbaIndex === index
                        }"
                        @click="selectTabs(index)"
                    >
                        {{ item.label }}
                    </div>
                </div>

                <div
                    class="2xl:max-w-[880px] xl:max-w-[780px] lg:max-w-[680px] max-w-[680px] search w-full mt-4"
                >
                    <el-input
                        size="large"
                        class="2xl:h-[54px] xl:h-[48px] lg:h-[44px] rounded-[12px]"
                        style="--el-border-color: transparent"
                        v-model="keyword"
                        :clearable="true"
                        :prefix-icon="Search"
                        placeholder="请输入关键词搜索"
                    >
                    </el-input>
                </div>
            </header>

            <!--        内容        -->
            <div class="flex-1 min-h-0 mx-[16px] relative" v-if="componentShow">
                <component
                    :is="currentComponent"
                    :keyword="keyword"
                />
            </div>
        </div>
    </NuxtLayout>
</template>

<script setup lang="ts">
import {Search} from '@element-plus/icons-vue'
import {getDecorate} from '~/api/app'
import {useAppStore} from '~/stores/app'
import {useUserStore} from '~/stores/user'

import Robot from './_components/robot.vue'
import Draw from './_components/draw.vue'
import Music from './_components/music.vue'
import Video from './_components/video.vue'

const route = useRoute()
const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()

const tabList = [
    { label: '智能体', value: 0, component: Robot },
    { label: 'AI绘画', value: 1, component: Draw },
    { label: 'AI音乐', value: 2, component: Music },
    { label: 'AI视频', value: 3, component: Video }
]
const tbaIndex = ref<number>(0)

const keyword = ref<string>('')
const currentComponent = ref(Robot)
const componentShow = ref<boolean>(true)

const {data: pages} = await useAsyncData(
    () => getDecorate({id: 6}),
    {
        lazy: true,
        default() {
            return []
        },
        transform: (value) => {
            return JSON.parse(value.data)
        }
    }
)

const getTitleColor = computed(() => {
    return (type: number) => {
        switch (type) {
            case 1:
                return 'text-black'
            case 2:
                return 'text-white'
            case 3:
                return 'text-primary'
        }
    }
})

const selectTabs = async (index: number) => {
    if (tbaIndex.value === index) return
    keyword.value = ''
    tbaIndex.value = index
    componentShow.value = false
    currentComponent.value = tabList[tbaIndex.value].component
    await nextTick()
    componentShow.value = true
    router.replace({
        path: '',
        query: {
            type: index
        }
    })
}

onMounted(async () => {
    const type = route.query.type
    if (type) {
        tbaIndex.value = Number(type)
        currentComponent.value = tabList[tbaIndex.value].component
    }
})

definePageMeta({
    layout: false,
    showLogo: true,
    hiddenFooter: true
})
</script>

<style lang="scss" scoped>
.robot-square-header {
    height: 300px;
    background-size: 100% 100%;
    background-repeat: no-repeat;

    .tabs-list {
        .tabs-item {
            display: flex;
            align-items: center;
            justify-content: center;

            white-space: nowrap;
            cursor: pointer;
            padding: 4px 12px;
            box-shadow: 0 2px 4px 0 #1e1e1f0a;
            border-radius: 12px;
            height: 32px;
            font-size: 14px;
            font-weight: 600;
            color: #4B4A58;
            background-color: #fff;
            @apply dark:bg-[#333] dark:text-white;

            &.is-active {
                @apply text-white bg-primary;
            }

            &.is-active:hover {
                @apply text-white bg-primary;
            }
        }

        .tabs-item:hover {
            @apply dark:text-[#4B4A58];
            background-color: #e6e6e9;
        }
    }

    .search {
        margin-top: 30px;

        :deep(.el-input) {
            .el-input__wrapper {
                padding-left: 20px;
            }
        }
    }
}
</style>
