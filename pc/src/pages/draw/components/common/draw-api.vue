<template>
    <div
        v-if="modelList.length > 1"
        class="bg-[var(--el-bg-color-page)] rounded-[12px] h-[50px]">
        <swiper
            class="draw_type_swiper h-full"
            :slidesPerView="'auto'"
            :spaceBetween="16"
            :speed="500"
            @swiper="onSwiper">
            <swiper-slide
                v-for="(item, index) in modelList"
                class="cursor-pointer"
                :key="item.model"
                style="width: auto">
                <div
                    class="tabs-item h-full flex justify-center pt-[10px]"
                    :class="{
                        'tabs-item__active': item.model == currentModel
                    }"
                    @click="handleClick(index)">
                    {{ item.name }}
                </div>
            </swiper-slide>
        </swiper>
    </div>
</template>

<script lang="ts" setup>
import { Swiper as SwiperInstance } from 'swiper'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)

const currentModel = ref<string>('sd')
const modelList = [
    {
        name: 'SD绘图',
        model: 'sd',
        balance: 0,
        default: false,
        member_free: true
    }
    // {
    //     name: 'DALLE绘图',
    //     model: 'dalle3',
    //     balance: 0,
    //     default: false,
    //     member_free: true
    // }
    // {
    //     name: '意间-SD',
    //     model: 'yijian_sd',
    //     balance: 0,
    //     default: false,
    //     member_free: true
    // },
    // {
    //     name: '知数云-慢速',
    //     model: 'zhishuyun_relax',
    //     balance: 0,
    //     default: false,
    //     member_free: true
    // },
    // {
    //     name: '官方直连-MJ',
    //     model: 'mddai_mj',
    //     balance: 0,
    //     default: false,
    //     member_free: true
    // },
    // {
    //     name: '知数云-快速',
    //     model: 'zhishuyun_fast',
    //     balance: 0,
    //     default: true,
    //     member_free: true
    // },
    // {
    //     name: '知数云-极速',
    //     model: 'zhishuyun_turbo',
    //     balance: 0,
    //     default: false,
    //     member_free: true
    // }
]

const swiperInstance = shallowRef<SwiperInstance>()

const onSwiper = (swiper: SwiperInstance) => {
    swiperInstance.value = swiper
}

const handleClick = (index: number) => {
    emit('update:modelValue', modelList[index].model)
    currentModel.value = modelList[index].model
    if (swiperInstance.value) {
        swiperInstance.value.slideTo(--index, 500, false)
    }
}
</script>

<style lang="scss" scoped>
.draw_type_swiper {
    .swiper-slide {
        margin-right: 15px !important;
    }
    .swiper-slide:first-child .tabs-item {
        margin-left: 15px;
    }
    .swiper-slide:last-child .tabs-item {
        margin-right: 15px;
    }
    .swiper-wrapper {
        @apply gap-4;
    }
    .tabs-item {
        position: relative;
        @apply text-lg text-tx-regular;
    }
    .tabs-item__active {
        @apply text-[#101010] dark:text-white font-medium;
        &::after {
            content: '';
            position: absolute;
            transform: translateX(-50%);
            left: 50%;
            width: 40%;
            height: 3px;
            bottom: 10px;
            border-radius: 2px;
            background-color: var(--el-color-primary);
        }
    }
}
</style>
