<template>
    <u-popup v-model="showModel" mode="left" width="80%" closeable>
        <view class="h-screen flex flex-col">
            <view
                class="text-xl font-bold border-b border-light border-0 border-solid px-[20rpx] py-[30rpx]"
            >
                思维导图
            </view>
            <view class="flex-1 min-h-0 overflow-hidden">
                <view class="p-[20rpx] h-full flex flex-col">
                    <view>
                        <view class="text-xl font-medium">
                            <text>帮我生成</text>
                            <text class="text-error">*</text>
                        </view>
                        <view class="mt-[20rpx]" @touchmove.stop>
                            <u-input
                                class="bg-page rounded"
                                v-model="userInput"
                                type="textarea"
                                :custom-style="{
                                    height: '250rpx',
                                    padding: '10rpx',
                                    'font-size': '28rpx',
                                    'border-radius': '8rpx'
                                }"
                                maxlength="-1"
                                :auto-height="false"
                                placeholder="请输入简单描述，AI将智能输出markdown内容"
                                @click="handleLogin"
                            />
                        </view>
                    </view>
                    <view class="mt-[40rpx] flex-1 min-h-0 flex flex-col">
                        <view class="flex items-center">
                            <text class="text-xl font-medium"> 需求描述 </text>
                            <text
                                class="ml-auto text-primary"
                                v-if="configs.is_example"
                                @click="useExample"
                            >
                                试试示例
                            </text>
                        </view>
                        <view
                            class="mt-[20rpx] flex-1 min-h-0 bg-page rounded"
                            @touchmove.stop
                        >
                            <textarea
                                class="w-full h-full p-[10rpx] text-base box-border"
                                v-model="descModel"
                                maxlength="-1"
                                type="textarea"
                            />
                        </view>
                    </view>
                </view>
            </view>
            <view class="p-[20rpx]">
                <u-button
                    type="primary"
                    :loading="isReceiving"
                    :custom-style="{ width: '100%' }"
                    @click="handleCreate"
                >
                    生成思维导图
                    <span class="text-sm ml-[20rpx]" v-if="configs.member_free">
                        会员免费
                    </span>
                    <span
                        class="text-sm ml-[20rpx]"
                        v-else-if="configs.balance > 0"
                    >
                        消耗 {{ configs.balance }} 电力值
                    </span>
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { ref, shallowRef, watch } from 'vue'
import { useUserStore } from '@/stores/user'
import { chatSendText } from '@/api/chat'
import { getMindMapConfig } from '@/api/app'
import { useRouter } from 'uniapp-router-next'

//#ifdef H5
import { isMiniProgram } from '@/utils/env'
import wechat from '@/utils/wechat'
//#endif

import { useAppStore } from '@/stores/app'
const appStore = useAppStore()

const props = defineProps<{
    show: boolean
}>()

const emit = defineEmits<{
    (event: 'update:show', show: boolean): void
    (event: 'update', value: string): void
}>()
const router = useRouter()
const showModel = useVModel(props, 'show', emit)
const descModel = ref('')
watch(descModel, (value) => {
    emit('update', value)
})

const userInput = ref('')
const userStore = useUserStore()
const isReceiving = ref(false)

const configs = ref<any>({})
const getConfigs = async () => {
    configs.value = await getMindMapConfig()
    !descModel.value && useExample()
}
getConfigs()
const currentIndex = ref(-1)
const useExample = () => {
    const length = configs.value.example_content?.length
    if (length) {
        let index = Math.round(Math.random() * (length - 1))
        if (currentIndex.value === index) {
            if (index < length - 1) {
                index++
            } else {
                index--
            }
        }
        currentIndex.value = index
    }
}

watch(currentIndex, (value) => {
    if (!configs.value.example_content) return
    const content = configs.value.example_content[value]
    if (content) {
        descModel.value = content
    }
})

const handleLogin = () => {
    if (userStore.isLogin) return
    router.navigateTo('/pages/login/login')
}

const handleCreate = async () => {
    if (!userStore.isLogin) {
        return toLogin()
    }
    if (isReceiving.value) return
    if (!userInput.value) return uni.$u.toast('请输入内容')

    try {
        isReceiving.value = true
        await chatSendText(
            {
                question: userInput.value,
                type: 4
            },
            {
                onstart(reader) {
                    descModel.value = ''
                },
                onmessage(value) {
                    value
                        .trim()
                        .split('data:')
                        .forEach(async (text) => {
                            if (text !== '') {
                                try {
                                    const dataJson = JSON.parse(text)
                                    const { object, choices, error } = dataJson
                                    if (error) {
                                        console.log(error)
                                        const { message, code } = error
                                        if (code === 1100) {
                                            if (!appStore.getIsShowRecharge) {
                                                uni.$u.toast(
                                                    `${appStore.getTokenUnit}数量已用完。请联系客服增加`
                                                )
                                            } else {
                                                const { cancel } =
                                                    await uni.showModal({
                                                        title: '温馨提示',
                                                        content: `${appStore.getTokenUnit}数量已用完，请前往充值`
                                                    })
                                                if (cancel) return
                                                router.navigateTo({
                                                    path: '/packages/pages/recharge/recharge'
                                                })
                                            }
                                        } else {
                                            message && uni.$u.toast(message)
                                        }

                                        return
                                    }

                                    switch (object) {
                                        case 'chat': {
                                            const data =
                                                choices[0]?.delta?.content
                                            descModel.value += data
                                        }
                                    }
                                } catch (error) {
                                    isReceiving.value = false
                                }
                            }
                        })
                },
                onclose() {
                    isReceiving.value = false
                }
            }
        )
    } catch (error: any) {
        isReceiving.value = false
    }
}

const toLogin = () => {
    if (isMiniProgram) {
        return wechat.miniProgram.navigateTo({ url: '/pages/login/login' })
    } else {
        return router.navigateTo({ path: '/pages/login/login' })
    }
}

defineExpose({
    changDescInput(value: string) {
        descModel.value = value
    }
})
</script>
