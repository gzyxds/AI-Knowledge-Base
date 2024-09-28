<template>
    <div v-if="isHidden" class="index-menu px-[15px] pb-4 pt-[15px]">
        <div class="text-xl font-medium mb-[15px]">
            {{ prop.title }}
        </div>
        <div
            class="grid gap-4"
            :style="{
                'grid-template-columns': `repeat(${ prop.showType }, minmax(0, 1fr))`
            }"
        >
            <div
                class="flex flex-col min-h-0 rounded-[20rpx] px-[24rpx] py-[30rpx] bg-white overflow-hidden"
                v-for="(item, index) in prop.data"
                :key="index"
                @click="handleClick(item.id)"
            >
                <div class="flex items-center">
                    <div class="flex-none">
                        <u-image :src="getImageUrl(item.image)" width="80" height="80" border-radius="12" />
                    </div>
                    <div class="text-lg font-medium ml-[20rpx] line-clamp-2">
                        {{ item.name }}
                    </div>
                </div>

                <div class="flex-1 mt-[18rpx] text-sm text-[#999] line-clamp-3">{{ item.tips }}</div>
                <div class="flex justify-end mt-[24rpx]">
                    <div class="text-muted flex items-center text-base">
                        <image
                            src="@/static/images/icon/icon_use.png"
                            class="w-[34rpx] h-[34rpx]"
                        ></image>
                        <text class="ml-[4rpx]">{{ item.user_count }}</text>
                    </div>
                    <div
                        class="text-muted flex items-center text-base ml-[50rpx]"
                        @click.stop="handleCollect(item, index)"
                    >
                        <u-icon v-if="!item.is_collect" name="star" size="32" color="#9a9a9a"></u-icon>
<!--                        <image-->
<!--                            v-if="!item.is_collect"-->
<!--                            src="/static/images/icon/icon_star.png"-->
<!--                            class="w-[34rpx] h-[34rpx]"-->
<!--                        ></image>-->
                        <u-icon v-else name="star-fill" size="32" color="#ffcd2c"></u-icon>
                        <text class="ml-[4rpx]">{{ item.collect_count }}</text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref, watch} from "vue";
import {navigateTo} from "@/utils/navigate";
import {useAppStore} from "@/stores/app";
import {collection} from '@/api/create'
import {useUserStore} from "@/stores/user";
import {useRouter} from "uniapp-router-next";

const router = useRouter();
const userStore = useUserStore();
const {getImageUrl} = useAppStore();
const props = defineProps<{
    isHidden: boolean
    prop: Record<string, any>
}>()

const adList = ref<Record<string, any>>([])

watch(
    () => props.prop.data,
    (val) => {
        adList.value = val
    },
    {deep: true, immediate: true}
)

const handleClick = (id: any) => {
    router.navigateTo({
        path: '/packages/pages/create/create',
        query: {
            id
        }
    })
};

const handleCollect = async (row: any, index) => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    try {
        await collection({
            id: row.id
        })
        props.prop.data[index].collect_count += 1
        props.prop.data[index].is_collect = !row.is_collect
    } catch (e) {
        console.log('收藏失败', e)
    }
}
</script>