<template>
    <div>
        <NuxtLayout name="default">
            <div class="app-center">
                <header
                    class="header flex-none flex flex-col justify-center items-center m-[16px] rounded-[12px] overflow-hidden"
                    :style="{
                        'background-image': `url(${getImageUrl(
                            pages?.pc_background
                        )})`
                    }"
                >
                    <div
                        class="font-medium 2xl:text-[50px] xl:text-[40px] lg:text-[36px] text-[36px]"
                        :class="getTitleColor(pages?.pc_text_color)"
                    >
                        {{ pages?.pc_title }}
                    </div>

                    <div
                        class="2xl:max-w-[880px] xl:max-w-[780px] lg:max-w-[680px] max-w-[680px] search w-full mt-4"
                    >
                        <el-input
                            size="large"
                            class="2xl:h-[54px] xl:h-[48px] lg:h-[44px]"
                            style="--el-border-color: transparent"
                            v-model="searchQuery"
                            :prefix-icon="Search"
                            placeholder="输入您想搜索的应用"
                            @input="filterApps"
                        >
                        </el-input>
                    </div>
                </header>

                <div>
                    <Waterfall
                        ref="waterFull"
                        :delay="100"
                        :list="filteredApps"
                        :width="364"
                        :gutter="20"
                        :animationDelay="0"
                        :animationDuration="0"
                        backgroundColor="none"
                        animationPrefix="none"
                        animated="none"
                        animationEffect="none"
                        :breakpoints="breakpoints"
                    >
                        <template #item="{ item }">
                            <div
                                class="card-item px-[20px] py-[24px] bg-body rounded-[12px] gap-y-[20px]"
                            >
                                <NuxtLink :to="item.pcLink.path">
                                    <div class="flex justify-between">
                                        <div class="mr-2">
                                            <div
                                                class="text-tx-primary text-xl"
                                            >
                                                {{ item.title }}
                                            </div>
                                            <div
                                                class="text-tx-secondary text-base mt-[10px]"
                                            >
                                                {{ item.desc }}
                                            </div>

                                            <div class="mt-[20px] show-btn">
                                                <ElButton
                                                    class="!border-none"
                                                    type="primary"
                                                >
                                                    去试试
                                                </ElButton>
                                            </div>
                                        </div>
                                        <div class="flex-none py-[8px]">
                                            <el-image
                                                :src="getImageUrl(item.image)"
                                                class="w-[82px] h-[82px] rounded-[18px]"
                                            >
                                            </el-image>
                                        </div>
                                    </div>
                                </NuxtLink>
                            </div>
                        </template>
                    </Waterfall>

                    <div
                        class="flex flex-col justify-center items-center w-full"
                        v-show="!filteredApps.length"
                    >
                        <el-image
                            class="w-[200px] h-[200px]"
                            :src="EmptyLayer"
                        />
                        <div class="text-tx-regular mb-4">
                            找不到更多应用了～
                        </div>
                    </div>
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { Search } from '@element-plus/icons-vue'
import { getDecorate } from '~/api/app'
import EmptyLayer from 'assets/image/empty_con.png'

const { getImageUrl } = useAppStore()

const { data: pages } = await useAsyncData(() => getDecorate({ id: 11 }), {
    transform: (value) => {
        const content = JSON.parse(value.data)[0].content
        content.data =
            content.data.filter((item: any) => item.is_show == 1) || []
        setTimeout(filterApps, 0)
        return content
    },
    default() {
        return []
    },
    lazy: true
})

const searchQuery = ref('')
const filteredApps = ref([])
const breakpoints: Record<number, { rowPerView: number }> = {
    4000: { rowPerView: 6 },
    2000: { rowPerView: 5 },
    1800: { rowPerView: 4 },
    1600: { rowPerView: 4 },
    1440: { rowPerView: 4 },
    1360: { rowPerView: 3 },
    1280: { rowPerView: 3 },
    1024: { rowPerView: 3 }
}

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

// 过滤出合适的描述或者标题
const filterApps = () => {
    const query = searchQuery.value.trim().toLowerCase()
    if (query === '') {
        filteredApps.value = pages.value.data
    } else {
        filteredApps.value = pages.value.data.filter((item: any) => {
            const title = item.title.toLowerCase()
            const desc = item.desc.toLowerCase()
            return title.includes(query) || desc.includes(query)
        })
    }
    console.log(query)
}

definePageMeta({
    layout: false,
    showLogo: true
})
</script>

<style lang="scss" scoped>
.app-center {
    .header {
        height: 300px;
        background-size: 100% 100%;
        background-repeat: no-repeat;

        .search {
            margin-top: 30px;

            :deep(.el-input) {
                .el-input__wrapper {
                    padding-left: 20px;
                }
            }
        }
    }

    .card-item {
        .show-btn {
            opacity: 0;
            transition: opacity 500ms ease-in-out;
        }
    }

    .card-item:hover {
        .show-btn {
            opacity: 1;
        }
    }
}
</style>