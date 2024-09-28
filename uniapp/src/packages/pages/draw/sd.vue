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
            v-if="!appStore.config.switch.sd_status"
            class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
        >
            <u-empty text="绘画功能未开启" mode="favor"></u-empty>
            <tabbar />
        </view>

        <template v-else>
            <u-navbar back-text="绘画工作台">
                <template #right>
                    <router-navigate to="/packages/pages/draw_list/draw_list?model=sd">
                        <view class="flex items-center mr-[20rpx]">
                            <view class="text-primary flex">
                                <u-icon name="clock" :size="32" />
                            </view>
                            <view class="text-muted ml-[10rpx]">历史记录</view>
                        </view>
                    </router-navigate>
                </template>
            </u-navbar>
            <view class="p-[20rpx]">
                <view class="flex p-[14rpx] bg-white rounded-full">
                    <view
                        v-for="item in modeList"
                        :key="item.mode"
                        class="flex items-center px-[10rpx] py-[5rpx] flex-1 justify-center text-primary rounded-full"
                        :class="{
                            '!bg-primary !text-white':
                                formData.draw_type === item.mode
                        }"
                        @click="formData.draw_type = item.mode"
                    >
                        <view class="leading-[30px]">{{ item.name }}</view>
                    </view>
                </view>
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="px-[20rpx] pb-[30rpx]">
                        <u-form
                            ref="uFormRef"
                            :model="formData"
                            label-position="top"
                            :border-bottom="false"
                        >
                            <!--    图片上传    -->
                            <uploader-picture
                                v-model="formData.image_mask"
                                v-if="formData.draw_type === DrawTypeEnum.IMAGE"
                            />
                            <!--    关键词    -->
                            <prompt
                                v-model="formData.prompt"
                                v-model:negative="formData.negative_prompt"
                                :model="DrawModeEnum.SD"
                                is-negative
                                :example="drawConfig.example"
                                :typeEnum="DrawTypeEnum"
                                :showTranslate="!!drawConfig.config.translate_switch"
                            />
                            <!--    生成尺寸    -->
                            <sd-size v-model="formData.size" />
                            <!--    生成模型    -->
                            <sd-model
                                :cate-list="drawConfig.model.cate"
                                :model-list="drawConfig.model.list"
                                v-model="formData.draw_model"
                                @change-cate="getModel($event)"
                            />
                            <!--    Lora模型    -->
                            <sd-lora
                                v-model="formData.draw_loras"
                                :model-list="drawConfig.model.list"
                                :current-lora="formData.draw_model"
                            />
                            <!--    高级参数    -->
                            <sd-options
                                :samplers="drawConfig.samplers"
                                v-model="formData.complex_params"
                            />
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
        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { reactive, ref, shallowRef } from 'vue'
import { useRoute, useRouter } from 'uniapp-router-next'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { useLockFn } from '@/hooks/useLockFn'
import { onShow, onLoad } from '@dcloudio/uni-app'

import {
    drawing,
    getSdConfig,
    getModelCategoryList,
    getModelList,
    getSdSamplersList
} from '@/api/draw'
import { DrawFormData } from '@/api/draw'
import { DrawTypeEnum, DrawModeEnum } from './enum/index'

import prompt from './components/prompt.vue'
import SdSize from './components/sd/sd-size.vue'
import SdModel from './components/sd/sd-model.vue'
import SdLora from './components/sd/sd-lora.vue'
import SdOptions from './components/sd/sd-options.vue'
import UploaderPicture from './components/uploader-picture.vue'
import {series} from "@/utils/util";

const router = useRouter()
const route = useRoute()
const uFormRef = shallowRef()
const appStore = useAppStore()
const userStore = useUserStore()



const modeList = ref([
    {
        mode: DrawTypeEnum.TEXT,
        name: '文生图'
    },
    {
        mode: DrawTypeEnum.IMAGE,
        name: '图生图'
    }
])

const formData = ref<DrawFormData>({
    draw_api: DrawModeEnum.SD,
    draw_type: DrawTypeEnum.TEXT,
    draw_model: '', // 绘画使用模型
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
    }
})

const drawConfig = reactive<any>({
    model: {
        cate: [],
        list: []
    },
    config: {},
    loras: [],
    samplers: []
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
 * 获取SD绘画模型分类
 */
const getModelCate = async () => {
    return getModelCategoryList().then(async (res) => {
        const data = res.map((item: any) => {
            return {
                value: item.id,
                label: item.name
            }
        })
        drawConfig.model.cate = [
            { label: '全部', value: 0 },
            ...data
        ]
        await getModel()
    })
}
/**
 * 获取SD绘画模型列表
 */
const getModel = async (id: number = 0) => {
    const lists = await getModelList({
        category_id: id
    })
    drawConfig.model.list = lists
}
/**
 * 获取SD绘画采样器列表
 */
const getSamplersList = async () => {
    drawConfig.samplers = await getSdSamplersList()
}

/**
 * 获取总数据
 */
const getData = (() => {
    return series(getConfig, getModelCate, getModel, getSamplersList)
})()

/**
 * 立即生成
 */
const { lockFn: handelDrawGenerate, isLock: isLockGenerate } = useLockFn(
    async () => {
        try {
            if (formData.value.draw_type === DrawTypeEnum.IMAGE) {
                if (formData.value.image_mask === '') {
                    return uni.$u.toast('请上传参考图')
                }
            }
            console.log(formData.value.prompt)
            if (formData.value.prompt === '') {
                return uni.$u.toast('请输入提示词')
            }
            if (formData.value.draw_api === '') {
                return uni.$u.toast('请选择主要模型')
            }
            if (formData.value.draw_type !== 'img2img') {
                formData.value.image_mask = ''
            }
            await drawing({
                ...formData.value
            })
            formData.value.prompt = ''
            router.navigateTo('/packages/pages/draw_list/draw_list?model=sd')
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
    } catch (error) {}
})
</script>
<style lang="scss">
page {
    height: 100%;
    overflow: hidden;
}
</style>
