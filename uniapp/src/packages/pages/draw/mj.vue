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
            v-if="!appStore.config.switch.mj_status"
            class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
        >
            <u-empty text="绘画功能未开启" mode="favor"></u-empty>
            <tabbar/>
        </view>
        <template v-else>
            <u-navbar back-text="绘画工作台">
                <template #right>
                    <router-navigate
                        :to="{
                            path: '/packages/pages/draw_list/draw_list',
                            query: { model: DrawModeEnum.MJ_GOAPI }
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
                                :is-negative="true"
                                :model="DrawModeEnum.MJ_GOAPI"
                                :example="drawConfig.example"
                                :typeEnum="DrawTypeEnum"
                                :showTranslate="!!drawConfig.config.translate_switch"
                            />

                            <!--    图片上传    -->
                            <uploader-picture v-model="formData.image_mask"/>

                            <!--    图片尺寸    -->
                            <mj-size v-model="formData.size"/>

                            <!-- 绘画模型 -->
                            <mj-model v-model="formData.draw_model"/>

                            <!-- 版本选择 -->
                            <mj-version
                                v-model="formData.version"
                                :draw-model="formData.draw_model"
                                :versionList="drawConfig.config?.mj_version[formData.draw_model]"
                            />

                            <!-- 风格选择 -->
                            <mj-styles
                                v-if="formData.version == 5 && formData.draw_model == 'niji'"
                                :styles-list="drawConfig.config?.mj_style"
                                v-model="formData.style"
                            />

                            <!-- 高级参数 -->
                            <mj-options v-model="formData.complex_params" :q-list="drawConfig.config.mj_quality" />
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="p-[20rpx] bg-white safe-area-inset-bottom">
                <u-button
                    type="primary"
                    :loading="isLockGenerate"
                    @click="handelDrawGenerate('generate')"
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
import {DrawFormData, ComplexParams} from '@/api/draw'
import {DrawTypeEnum, DrawModeEnum} from './enum/index'

import prompt from './components/prompt.vue'
import UploaderPicture from './components/uploader-picture.vue'
import MjSize from './components/mj/mj-size.vue'
import MjModel from './components/mj/mj-model.vue'
import MjVersion from './components/mj/mj-version.vue'
import MjStyles from './components/mj/mj-styles.vue'
import MjOptions from './components/mj/mj-options.vue'
import {series} from "@/utils/util";

const router = useRouter()
const route = useRoute()
const uFormRef = shallowRef()
const appStore = useAppStore()
const userStore = useUserStore()

const formData = ref<DrawFormData>({
    draw_api: DrawModeEnum.MJ_GOAPI,
    draw_type: DrawTypeEnum.TEXT,
    draw_model: 'mj', // 绘画使用模型
    size: '1:1', // 图片尺寸
    prompt: '', // 正向提示词
    negative_prompt: '', // 负向提示词
    action: 'generate', // 绘画操作
    image_mask: '', // 图生图初始蒙版
    image_id: '', // 图片放大初始图片id
    complex_params: {
        seed: '',
        iw: 1,
        q: "1",
        s: 100,
        c: 0
    } as ComplexParams,
    style: '', // mj 风格

    version: '', // mj_goapi 版本
    origin_task_id: '' // mj_goapi 绘画任务ID (变图，放大等操作时必传)
})

const drawConfig = reactive<any>({
    config: {
        mj_version: {},
        mj_style: {},
        mj_quality: {}
    },
})
/**
 * 获取MJ绘画公共数据
 */
const getConfig = async () => {
    drawConfig.config = await getSdConfig({
        draw_api: DrawModeEnum.MJ_GOAPI
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
    async (value: string) => {
        try {
            if (value) {
                formData.value.action = value
            }
            if (formData.value.prompt === '') {
                return uni.$u.toast('请输入提示词')
            }
            await drawing({
                ...formData.value
            })
            formData.value.prompt = ''
            router.navigateTo({
                path: '/packages/pages/draw_list/draw_list',
                query: {model: DrawModeEnum.MJ_GOAPI}
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
        if (data.action !== 'generate') {
            handelDrawGenerate(data.action)
        }
    })
    const query = route.query
    try {
        const data = JSON.parse(query.data as string)
        Object.assign(formData.value, data)

        if (data.action !== 'generate') {
            handelDrawGenerate(data.action)
        }
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
