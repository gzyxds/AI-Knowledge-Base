<template>
    <div class="entrance pt-[40px]">
        <div class="flex w-[1200px] mx-auto justify-center">
            <div
                class="grid flex-wrap"
                :style="{
                    'grid-template-columns': `repeat(${ props.prop.showType }, minmax(0, 1fr))`
                }"
            >
                <div class="flex-1 mb-[40px]" v-for="(item, index) in getShowData" :key="index">
                    <div class="chat-card h-full" :to="item.path">
                        <div v-if="item.icon" class="mb-[10px]">
                            <decoration-img
                                :src="item.icon"
                                width="58px"
                                height="58px"
                                radius="8px"
                            />
                        </div>
                        <div class="text-2xl font-medium">{{ item.title }}</div>
                        <div class="line w-[100%] mt-4" />
                        <div class="my-4 text-sm h-[80px] leading-[20px] line-clamp-4 px-[20px]">
                            {{ item.desc }}
                        </div>
                        <div class="enter-btn">
                            <ElButton link type="primary">立即前往 ></ElButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { Prop } from './config'
import DecorationImg from '../../decoration-img.vue'
const props = defineProps<{
    prop: Prop
}>()
const getShowData = computed(() => props.prop.data.filter((item) => item.isShow))
</script>

<style lang="scss" scoped>
.chat-card {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
    border-radius: 10px;
    transition: all 0.5s;
    flex-direction: column;
    box-shadow: 0px 5px 10px var(--el-color-primary-light-9);

    background-color: #fff;
    margin: 0 20px;
    .enter-btn {
        opacity: 0;
    }
    &:hover {
        .enter-btn {
            opacity: 1;
        }
    }
}
</style>
