<template>
    <u-form-item label="选择风格">
        <view class="flex-1 min-w-0 bg-white rounded-[16rpx]">
            <swiper
                class="h-[520rpx]"
                circular
                :indicator-dots="swiperNum > 1"
                :indicator-active-color="$theme.primaryColor"
            >
                <swiper-item v-for="(i, index) in swiperNum" :key="index">
                    <view
                        class="flex flex-wrap px-[20rpx] pt-[20rpx] mx-[-10rpx]"
                    >
                        <view
                            v-for="item in sliceStyle(index)"
                            class="w-[25%] px-[10rpx] mb-[10rpx]"
                            :key="item.id"
                        >
                            <view
                                class="h-full cursor-pointer"
                                @click="selectStyle(item.id)"
                            >
                                <view
                                    class="pt-[100%] relative h-0 rounded-[10rpx] overflow-hidden"
                                >
                                    <view class="absolute inset-0 left-0 top-0">
                                        <u-image
                                            :src="item.image"
                                            class="h-full w-full"
                                            width="100%"
                                            height="100%"
                                        />
                                    </view>
                                    <view
                                        v-if="
                                            styleIds.includes(item.id) ||
                                            styleIds.includes(String(item.id))
                                        "
                                        class="absolute bg-[rgba(0,0,0,0.5)] inset-0 left-0 top-0 flex items-center justify-center text-white"
                                    >
                                        <u-icon
                                            name="checkmark-circle-fill"
                                            :size="40"
                                        />
                                    </view>
                                </view>
                                <view class="text-center text-xs">
                                    {{ item.name }}
                                </view>
                            </view>
                        </view>
                    </view>
                </swiper-item>
            </swiper>
        </view>
    </u-form-item>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import { computed, reactive, ref, shallowRef } from 'vue'
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        styleList: any[]
    }>(),
    {
        modelValue: () => []
    }
)
const { modelValue: styleIds } = useVModels(props, emit)

const sliceNum = ref(8)
const sliceStyle = (index: number) => {
    return props.styleList.slice(
        sliceNum.value * index,
        sliceNum.value * (index + 1)
    )
}

const swiperNum = computed(() => {
    return Math.ceil(props.styleList.length / sliceNum.value)
})

const selectStyle = (id: number) => {
    const index = styleIds.value.findIndex((item: any) => item === id)
    if (index !== -1) {
        styleIds.value.splice(index, 1)
    } else {
        styleIds.value.push(id)
    }
}
</script>
