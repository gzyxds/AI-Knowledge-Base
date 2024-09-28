<template>
    <div class="xl:max-w-[1200px] flex justify-center mx-auto">
        <div
            class="grid flex-wrap"
            :style="{
                'grid-template-columns': `repeat(${props.prop.showType}, minmax(0, 1fr))`
            }"
        >
            <div
                class="flex-1 md:mb-[40px] mb-[20px]"
                v-for="(item, index) in getShowData"
                :key="index"
            >
                <AppLink
                    :to="{
                        path: item.link?.path,
                        query: item.link?.query
                    }"
                >
                    <div class="chat-card h-full" :to="item.link?.path">
                        <div v-if="item.icon" class="mb-[10px]">
                            <ElImage
                                class="w-[58px] h-[58px] rounded-lg"
                                :src="appStore.getImageUrl(item.icon)"
                            />
                        </div>
                        <div class="text-2xl font-medium">{{ item.title }}</div>
                        <div class="line w-[100%] mt-4"></div>
                        <div
                            class="my-4 text-sm h-[80px] leading-[20px] line-clamp-4"
                        >
                            {{ item.desc }}
                        </div>
                        <div class="enter-btn mt-3">
                            <!--            <ElButton link type="primary">立即前往 ></ElButton>-->
                            <img
                                src="~/assets/image/index_arrow-rigjt.png"
                                class="w-[32px] h-[32px]"
                                alt=""
                            />
                        </div>
                    </div>
                </AppLink>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'

const props = defineProps<{
    prop: Record<string, any>
}>()
const appStore = useAppStore()
const getShowData = computed(() =>
    props.prop.data.filter((item: any) => item.isShow)
)
</script>

<style lang="scss" scoped>
.chat-card {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 26px;
    border-radius: 10px;
    transition: all 0.5s;
    flex-direction: column;
    border: 1px solid transparent;
    box-shadow: 0 5px 10px var(--el-color-primary-light-9);
    background-color: #ffffff;

    &:hover {
        transform: translateY(-10px);
        border: 1px solid var(--el-color-primary);
        box-shadow: 0px 5px 10px var(--el-color-primary-light-8);
        background: linear-gradient(
            90deg,
            rgba(112, 195, 236, 0.3) 0%,
            rgba(66, 109, 247, 0.3) 100%
        );
    }

    @apply text-tx-primary sm:mx-[20px]  mx-0   bg-white;
}
</style>
