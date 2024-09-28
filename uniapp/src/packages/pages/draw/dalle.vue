<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col bg-[#f5f8fd]">
        <view
            v-if="!appStore.config.switch.dalle3_status"
            class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
        >
            <u-empty text="绘画功能未开启" mode="favor"></u-empty>
            <tabbar />
        </view>
        <template v-else>
            <u-navbar back-text="绘画工作台">
                <template #right>
                    <router-navigate
                        :to="{
                            path: '/packages/pages/draw_list/draw_list',
                            query: { model: DrawModeEnum.DALLE }
                        }"
                    >
                        <view class="flex items-center mr-[20rpx]">
                            <view class="text-primary flex">
                                <u-icon name="clock" :size="32"/>
                            </view>
                            <view class="text-muted ml-[10rpx]">历史记录</view>
                        </view>
                    </router-navigate>
                </template>
            </u-navbar>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="px-[20rpx] pb-[30rpx] pt-[6rpx]">
                        <u-form
                            ref="uFormRef"
                            :model="formData"
                            label-position="top"
                            :border-bottom="false"
                        >
                            <!--    关键词    -->
                            <prompt
                                v-model="formData.prompt"
                                v-model:negative="formData.negative_prompt"
                                :is-negative="false"
                                :model="DrawModeEnum.DALLE"
                                :example="drawConfig.example"
                                :typeEnum="DrawTypeEnum"
                                :showTranslate="!!drawConfig.config.translate_switch"
                            />

                            <!--    图片尺寸    -->
                            <dalle-size v-model="formData.size"/>

                            <!--    风格选择    -->
                            <dalle-style v-model="formData.style"/>

                            <!--    图片质量    -->
                            <dalle-quality v-model="formData.quality"/>
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="p-[20rpx] bg-white safe-area-inset-bottom">
                <u-button
                    type="primary"
                    :loading="isLockGenerate"
                    @click="handelDrawGenerate"
                >
                    <view>
                        <text class="text-xl font-bold">立即生成</text>
                        <text v-if="drawConfig.config.is_member" class="text-sm ml-[4px]">
                            会员免费
                        </text>
                        <text
                            v-if="
                                drawConfig.config.power != 0 &&
                                !drawConfig.config.is_member &&
                                userStore.isLogin
                            "
                            class="text-sm ml-[4px]"
                        >
                            消耗{{ drawConfig.config.power || '--' }}{{ appStore.getTokenUnit }}
                        </text>
                    </view>
                </u-button>
            </view>
        </template>
        <tabbar/>
    </view>
</template>

<script setup lang="ts">
import {reactive, ref, shallowRef} from 'vue'
import {useRoute, useRouter} from 'uniapp-router-next'
import {useAppStore} from '@/stores/app'
import {useUserStore} from '@/stores/user'
import {useLockFn} from '@/hooks/useLockFn'
import {onShow, onLoad} from '@dcloudio/uni-app'

import {
    drawing,
    getSdConfig
} from '@/api/draw'
import {DrawFormData} from '@/api/draw'
import {DrawTypeEnum, DrawModeEnum} from './enum/index'

import prompt from './components/prompt.vue'
import DalleSize from './components/dalle/dalle-size.vue'
import DalleStyle from './components/dalle/dalle-style.vue'
import DalleQuality from './components/dalle/dalle-quality.vue'
import {series} from "@/utils/util";

const router = useRouter()
const route = useRoute()
const uFormRef = shallowRef()
const appStore = useAppStore()
const userStore = useUserStore()

const formData = ref<DrawFormData>({
    draw_api: DrawModeEnum.DALLE,
    draw_type: DrawTypeEnum.TEXT,
    draw_model: DrawModeEnum.DALLE, // 绘画使用模型
    draw_loras: '', // lora 模型
    denoising_strength: 0.75, // 重绘强度
    size: '512x512', // 图片尺寸
    prompt: '', // 正向提示词
    negative_prompt: '', // 负向提示词
    action: 'generate', // 绘画操作
    image_mask: '', // 图生图初始蒙版
    image_id: '', // 图片放大初始图片id
    complex_params: {
        step: 20, // 采样步数
        sampler_name: 'Euler a', // 采样模式
        seed: -1, // 随机种子
        cfg_scale: 7 // 提示词系数
    },

    style: '', // dalle3 风格
    quality: '' // dalle3 图片质量
})

const drawConfig = reactive<any>({
    config: {},
})
/**
 * 获取SD绘画公共数据
 */
const getConfig = async () => {
    drawConfig.config = await getSdConfig({
        draw_api: DrawModeEnum.DALLE
    })
}

/**
 * 获取总数据
 */
const getData = (() => {
    return series(getConfig)
})()

/**
 * 立即生成
 */
const {lockFn: handelDrawGenerate, isLock: isLockGenerate} = useLockFn(
    async () => {
        try {
            if (formData.value.prompt === '') {
                return uni.$u.toast('请输入提示词')
            }
            await drawing({
                ...formData.value
            })
            formData.value.prompt = ''
            router.navigateTo({
                path: '/packages/pages/draw_list/draw_list',
                query: {model: DrawModeEnum.DALLE}
            })
        } catch (error) {
        } finally {
        }
    }
)

getData()

onLoad(() => {
    uni.$on('drawRegenerate', (data: any) => {
        Object.assign(formData.value, data)
    })
    const query = route.query
    try {
        const data = JSON.parse(query.data as string)
        Object.assign(formData.value, data)
    } catch (error) {
    }
})
</script>
<style lang="scss">
page {
    height: 100%;
    overflow: hidden;
}
</style>
