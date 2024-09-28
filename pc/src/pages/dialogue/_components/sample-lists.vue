<template>
    <div class="sample-lists-container sm:px-[10px]">
        <div
            class="sm:my-[60px] my-[30px] text-center text-[30px] font-medium"
        />
        <div class="flex sample-lists">
            <div
                v-for="item in showData"
                :key="item.id"
                class="flex-1 sm:mx-[10px] mx-[5px] p-[20px] sample-lists-item"
                :class="{
                    'is-dark': isDark
                }"
            >
                <div class="flex justify-center items-center mb-[20px]">
                    <img
                        v-if="item.image"
                        class="w-[35px] h-[35px]"
                        :src="item.image"
                        alt=""
                    />
                    <div class="text-2xl font-medium ml-3 text-tx-primary">
                        {{ item.name }}
                    </div>
                </div>
                <div>
                    <div
                        v-for="sample in sliceInData(item.sample, column)"
                        :key="sample.id"
                        class="bg-body sm:mb-[15px] mb-[10px] p-[10px] flex justify-center rounded-[12px] cursor-pointer"
                        @click="emit('click-item', sample.content)"
                    >
                        <div
                            class="flex-1 text-center line-clamp-1 text-sm sm:text-base"
                        >
                            {{ sample.content }}
                        </div>
                        <div class="flex-none flex items-center">
                            <icon
                                name="el-icon-Right"
                                color="inherit"
                                size="16"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="isShowMoreBtn">
            <div class="flex justify-center mt-10" @click="showMore = true">
                <el-button link>
                    查看更多
                    <Icon name="el-icon-ArrowRight" />
                </el-button>
            </div>
            <el-dialog
                v-model="showMore"
                width="600px"
                title="问题示例"
                class="sample-popup"
            >
                <el-tabs :model-value="0">
                    <el-tab-pane
                        v-for="(item, index) in samplesList"
                        :key="item.id"
                        :label="item.name"
                        :name="index"
                    >
                        <div class="h-[50vh]">
                            <ElScrollbar>
                                <div
                                    v-for="sample in item.sample"
                                    :key="sample.id"
                                    class="bg-page mb-[10px] p-[10px] flex justify-center rounded-[2px] cursor-pointer"
                                    @click="emit('click-item', sample.content)"
                                >
                                    <div class="line-clamp-2">
                                        {{ sample.content }}
                                    </div>
                                </div>
                            </ElScrollbar>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </el-dialog>
        </template>
    </div>
</template>

<script setup lang="ts">
import { useQuery } from '@tanstack/vue-query'
import { getChatSampleLists } from '~/api/chat'
import { useAppStore } from '~/stores/app'
import { useDark } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'click-item', value: string): void
}>()
const row = 3
const column = 3
const appStore = useAppStore()
const isDark = useDark()
const { data: samplesList, suspense } = useQuery(['samplesList'], {
    queryFn: getChatSampleLists,
    placeholderData: []
})
await useAsyncData(() => suspense(), {
    lazy: true
})

const sliceInData = (data: any[], len: number) => {
    return data.slice(0, len)
}
const showData = computed(() => {
    return sliceInData(samplesList.value, row)
})
const isShowMoreBtn = computed(() => {
    return (
        (samplesList.value as any[]).reduce((prev, item) => {
            prev += item.sample?.length || 0
            return prev
        }, 0) >
            row * column || samplesList.value.length > row
    )
})
const showMore = ref(false)
</script>
<style lang="scss" scoped>
.sample-lists-container {
    :deep(.sample-popup) {
        .el-dialog__body {
            padding-top: 0;
        }
    }

    .sample-lists {
    }

    .sample-lists-item {
        border-radius: 12px;

        .sample-lists-item__title {
            color: #333;
        }
    }

    .sample-lists-item:nth-child(1) {
        color: #0256ff;
        background: linear-gradient(
            90deg,
            rgba(112, 195, 236, 0.2) 0%,
            rgba(66, 109, 247, 0.2) 100%
        );

        &.is-dark {
            background: linear-gradient(
                90deg,
                rgba(112, 195, 236, 0.2) 0%,
                rgba(66, 109, 247, 0.2) 100%
            );
        }

        @apply dark:text-[#0256FF];
    }

    .sample-lists-item:nth-child(2) {
        color: #07b9b9;
        background: linear-gradient(86.9deg, #dddefc 0%, #d9fcfb 100%);

        &.is-dark {
            background: linear-gradient(
                88deg,
                rgba(79, 88, 254, 0.2) 1%,
                rgba(23, 248, 235, 0.2) 98%
            );
        }

        @apply dark:text-[#07B9B9];
    }

    .sample-lists-item:nth-child(3) {
        color: #ff8f1f;
        background: linear-gradient(86.9deg, #fbf7f2 1%, #f6dfc5 100%);

        &.is-dark {
            background: linear-gradient(
                88deg,
                rgba(245, 224, 201, 0.3) 2%,
                rgba(239, 143, 45, 0.3) 98%
            );
        }

        @apply dark:text-[#FF8F1F];
    }
}
</style>