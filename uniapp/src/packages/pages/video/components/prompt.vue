<template>
    <u-form-item :label="currentConfig.label" prop="title" required>
        <view class="flex-1 min-w-0">
            <l-textarea
                v-model="prompt"
                :rows="4"
                :custom-class="{
                    background: '#fff',
                    paddingBottom: '70rpx',
                    fontSize: '28rpx'
                }"
                :placeholder="currentConfig.placeholder"
                @click="handleLogin"
            >
                <template #length-suffix>
                    <view class="flex length-suffix">
                        <view class="flex-1 flex items-center">
                            <view
                                v-if="isText2Video"
                                class="inline-flex items-center text-primary mr-[10rpx]"
                                @click.stop="useExample"
                            >
                                <u-icon name="bookmark" size="24"></u-icon>

                                <text class="ml-[8rpx]">
                                    <text> 试试示例 </text>
                                </text>
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
                                <u-icon v-else name="reload" size="24"></u-icon>

                                <text class="ml-[8rpx]">
                                    <text> 翻译成英文 </text>
                                </text>
                            </view>
                        </view>
                        <view
                            class="flex items-center text-content text-xs ml-auto"
                            @click.stop="prompt = ''"
                        >
                            <u-icon name="trash" />
                            <view class="ml-[8rpx]"> 清空 </view>
                        </view>
                    </view>
                </template>
            </l-textarea>
        </view>
    </u-form-item>
</template>
<script lang="ts" setup>
import { translate } from '@/api/video'
import { useLockFn } from '@/hooks/useLockFn'
import { useVModels } from '@vueuse/core'
import { computed, reactive, ref, shallowRef } from 'vue'
import {useUserStore} from '@/stores/user'
import {useRouter} from 'uniapp-router-next'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue: string
        config: Record<string, any>
        showTranslate: boolean
        type: number
    }>(),
    {
        modelValue: '',
        config: () => ({}),
        showTranslate: false,
        type: 1
    }
)

const router = useRouter()
const userStore = useUserStore()
const { modelValue: prompt } = useVModels(props, emit)
const isText2Video = computed(() => props.type === 1)

const configMap = {
    '1': {
        label: '视频场景',
        placeholder: '在此描述你的视频场景，包含内容主体+动作/场景'
    },
    '2': {
        label: '描述词',
        placeholder: '描述视频中需要变化的内容即可～'
    }
}

const currentConfig = computed<any>(() => configMap[props.type as never] || {})
const currentIndex = ref(-1)

const handleLogin = () => {
    if (userStore.isLogin) return
    router.navigateTo('/pages/login/login')
}

const useExample = () => {
    const length = props.config.data?.length
    if (length) {
        let index = Math.round(Math.random() * (length - 1))
        if (currentIndex.value === index) {
            if (index < length - 1) {
                index++
            } else {
                index--
            }
        }
        if (index < 0) {
            index = 0
        }
        currentIndex.value = index
        const content = props.config.data[index]
        if (content) prompt.value = content
    }
}

const { lockFn: translatePrompt, isLock } = useLockFn(async () => {
    if (!prompt.value) {
        return uni.$u.toast('请输入描述词')
    }

    const data = await translate({
        prompt: prompt.value
    })
    prompt.value = data.result
})
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
