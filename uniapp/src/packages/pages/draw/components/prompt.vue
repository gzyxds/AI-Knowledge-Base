<template>
    <u-form-item prop="title" style="padding: 0">
        <view class="flex flex-1 bg-white rounded-lg overflow-hidden">
            <view class="flex-1">
                <!--      正面提示次词      -->
                <l-textarea
                    v-if="currentIndex == 1"
                    v-model="prompt"
                    :rows="4"
                    :custom-class="{
                        background: '#fff',
                        paddingBottom: '70rpx',
                        fontSize: '28rpx',
                        borderRadius: !isNegative ? '16rpx' : '16rpx 0 0 16rpx'
                    }"
                    :placeholder="currentConfig.placeholder"
                    @click="handleLogin"
                >
                    <template #length-suffix>
                        <view class="flex length-suffix">
                            <view
                                class="flex items-center text-content text-xs ml-auto"
                                @click.stop="prompt = ''"
                            >
                                <u-icon name="trash"/>
                                <view class="ml-[8rpx]"> 清空</view>
                            </view>
                        </view>
                    </template>
                </l-textarea>
                <!--      反向提示次词      -->
                <l-textarea
                    v-else
                    v-model="negativePrompt"
                    :rows="4"
                    :custom-class="{
                        background: '#fff',
                        paddingBottom: '70rpx',
                        fontSize: '28rpx',
                        borderRadius: '16rpx 0 0 16rpx'
                    }"
                    :placeholder="currentConfig.placeholder"
                    @click="handleLogin"
                >
                    <template #length-suffix>
                        <view class="flex length-suffix">
                            <view
                                class="flex items-center text-content text-xs ml-auto"
                                @click.stop="negativePrompt = ''"
                            >
                                <u-icon name="trash"/>
                                <view class="ml-[8rpx]"> 清空</view>
                            </view>
                        </view>
                    </template>
                </l-textarea>
            </view>

            <view class="flex-none" v-if="isNegative">
                <view
                    v-for="(item, index) in configMap"
                    :key="item.label"
                    :class="{
                        '!bg-primary !text-white': index == currentIndex
                    }"
                    class="flex justify-center items-center w-[72rpx] h-[150rpx] px-[20rpx] leading-[34rpx] rounded-r-lg text-main bg-[#f0f0f0]"
                    @click.stop="currentIndex = index"
                >
                    {{ item['label'] }}
                </view>
            </view>
        </view>
    </u-form-item>

    <view class="mt-[15rpx] p-[20rpx] rounded-lg bg-white">
        <view class="flex-1 flex items-center justify-between">
            <view
                class="flex items-center text-primary"
                @click="keywordSuggestionRef?.open()"
            >
                <image
                    class="w-[24rpx] h-[24rpx]"
                    :src="ExampleIcon"
                ></image>
                <text class="ml-[8rpx]">描述词推荐</text>
            </view>

            <view
                v-if="showTranslate"
                class="inline-flex items-center text-primary"
                @click.stop="translatePrompt"
            >
                <u-loading
                    mode="flower"
                    size="24"
                    v-if="isLock"
                ></u-loading>
                <image
                    v-else
                    class="w-[24rpx] h-[24rpx]"
                    :src="TranslateIcon"
                ></image>

                <text class="ml-[8rpx]">翻译成英文</text>
            </view>

            <view
                class="inline-flex items-center text-primary mr-[10rpx]"
                @click.stop="getDrawingExample"
            >
                <text class="ml-[8rpx]">试试示例</text>
            </view>
        </view>
    </view>

    <KeywordSuggestion
        ref="keywordSuggestionRef"
        :model="model"
        v-model="prompt"
    />
</template>
<script lang="ts" setup>
import {translate} from '@/api/draw'
import {useLockFn} from '@/hooks/useLockFn'
import {useVModel} from '@vueuse/core'
import {drawingExample} from "@/api/draw";
import {computed, ref, shallowRef} from 'vue'
import {useUserStore} from '@/stores/user'
import {useRouter} from 'uniapp-router-next'

import ExampleIcon from '../../../static/images/example.png'
import TranslateIcon from '../../../static/images/translate.png'
import KeywordSuggestion from './keyword-suggestion.vue'

const keywordSuggestionRef =
    shallowRef<InstanceType<typeof KeywordSuggestion>>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'update:negative', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue: string
        model: string
        negative: string
        showTranslate: boolean
        isNegative: boolean
    }>(),
    {
        modelValue: '',
        model: '',
        negative: '',
        showTranslate: false,
        isNegative: true
    }
)

const router = useRouter()
const userStore = useUserStore()
const prompt = useVModel(props, 'modelValue', emit)
const negativePrompt = useVModel(props, 'negative', emit)

const configMap: Record<number, { label: string; placeholder: string }> = {
    1: {
        label: '描述词',
        placeholder: '请输入英文描述词，生成效果会更好；描述词越详细，生成的图片效果更加真实哦~ ~'
    },
    2: {
        label: '反向词',
        placeholder: '输入你希望AI绘制的内容，例如：white hair,sit，这样AI尽可能避免绘制白色的毛发和坐着的姿势'
    }
}

const currentIndex = ref<number>(1)
const currentConfig = computed<any>(() => configMap[currentIndex.value as never] || {})

const handleLogin = () => {
    if (userStore.isLogin) return
    router.navigateTo('/pages/login/login')
}

const {lockFn: translatePrompt, isLock} = useLockFn(async () => {
    if (!prompt.value) {
        return uni.$u.toast('请输入描述词')
    }

    const data = await translate({
        prompt: prompt.value,
        model: props.model
    })
    prompt.value = data.result
})

// 获取随机绘画示例
const exampleIndex = ref<number>(0)
const exampleLists = ref<any[]>([])
const getDrawingExample = async () => {
    try {
        if (exampleLists.value.length) {
            if (exampleIndex.value >= exampleLists.value.length - 1) {
                exampleIndex.value = -1
            }
            exampleIndex.value += 1
            prompt.value = exampleLists.value[exampleIndex.value]?.prompt_en || ''
            return
        }
        exampleLists.value = await drawingExample({
            model: props.model
        })
        prompt.value = exampleLists.value[0]?.prompt_en || ''
    } catch (error) {
        console.log('请求绘画示例失败=>', error)
    }
}
</script>

<style lang="scss">
.length-suffix {
    position: absolute;
    bottom: 0;
    left: 0;
    z-index: 10;
    font-size: 24rpx;
    display: flex;
    align-items: center;
    width: 100%;
    height: 70rpx;
    padding: 0 24rpx;
}
</style>
