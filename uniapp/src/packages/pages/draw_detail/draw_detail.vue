<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status class="h-full" :status="status">
        <view class="h-full flex flex-col">
            <view class="flex-1 min-h-0">
                <view class="px-[20rpx] bg-white" style="padding-bottom: 50px">

                    <!--      绘画图片      -->
                    <view class="pt-[24rpx]">
                        <u-image
                            :src="draw.image"
                            width="100%"
                            height="100%"
                            mode="widthFix"
                            border-radius="30"
                        >
                            <template #loading>
                                <view
                                    class="flex flex-col justify-center items-center w-[640rpx] h-[640rpx] bg-[#F7F9FD]"
                                >
                                    <u-loading
                                        mode="circle"
                                        :color="$theme.primaryColor"
                                        size="40"
                                    ></u-loading>
                                    <view class="text-primary text-sm mt-[20rpx]">
                                        加载中
                                    </view>
                                </view>
                            </template>
                        </u-image>
                    </view>

                    <!--      绘画描述词      -->
                    <view class="mt-[30rpx]">
                        <view class="flex items-center justify-between">
                            <text class="text-lg font-medium">描述词</text>
                            <text class="text-primary text-base" @click="copy(draw.prompt_en)">复制</text>
                        </view>
                        <view class="min-h-[150rpx] mt-[20rpx] p-[20rpx] bg-page rounded-lg">
                            <text
                                class="text-base leading-4"
                                style="letter-spacing: 1px"
                            >
                                {{ draw.prompt }}
                            </text>
                        </view>
                    </view>

                    <!--      绘画反向词      -->
                    <view class="mt-[30rpx]">
                        <view class="flex items-center justify-between">
                            <text class="text-lg font-medium">反向词</text>
                            <text class="text-primary text-base" @click="copy(draw.negative_prompt)">复制</text>
                        </view>
                        <view class="min-h-[150rpx] mt-[20rpx] p-[20rpx] bg-page rounded-lg">
                            <text class="text-base">{{ draw.negative_prompt || '未填写' }}</text>
                        </view>
                    </view>

                    <!--      创作信息      -->
                    <view class="mt-[30rpx]" style="padding-bottom: 100rpx;">
                        <view class="text-lg font-medium">创作信息</view>
                        <view class="mt-[20rpx] p-[20rpx] bg-page rounded-lg text-main text-base">
                            <view class="flex items-center justify-between">
                                <text>创作者</text>
                                <text>{{ draw.nickname || userStore?.userInfo?.nickname }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>生成时间</text>
                                <text>{{ draw.create_time }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>绘画类型</text>
                                <text>{{ DrawResultTypeEnum[draw.type as 1 | 2 | 3 | 4] }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>绘画模型</text>
                                <text>{{ draw.engine }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]" v-if="draw.model == 'sd'">
                                <text>微调模型</text>
                                <view class="w-[400rpx] text-right line-clamp-1">
                                    {{ draw.loras.join('、') }}
                                </view>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>图片尺寸</text>
                                <text>{{ draw.scale }}</text>
                            </view>
                        </view>

                        <view class="mt-[20rpx] p-[20rpx] bg-page rounded-lg text-main text-base" v-if="draw.model == 'sd'">
                            <view class="flex items-center justify-between">
                                <text>绘画步数</text>
                                <text>{{ complexParams.step || '-' }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>文本强度</text>
                                <text>{{ complexParams.cfg_scale || '-' }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>采样模式</text>
                                <text>{{ complexParams.sampler_name || '-' }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>随机种子</text>
                                <text>{{ complexParams.seed || '-' }}</text>
                            </view>
                        </view>
                    </view>

                    <view
                        v-if="collectData.id"
                        class="draw-detail-footer flex justify-between items-center px-[60rpx]"
                    >
                        <view class="flex text-xs text-[#666]">
                            <view class="text-center" @click.stop="handlePraise">
                                <view class="praise">
                                    <view
                                        class="praise-animate"
                                        :style="{
                                            backgroundImage: `url(${domain}resource/image/api/default/praise02.png)`
                                        }"
                                        :class="[draw.is_collect ? 'praise-entry':'praise-leave']"
                                    >
                                    </view>
                                </view>
                                <view class="text-[22rpx] text-content">收藏</view>
                            </view>
                            <view class="ml-7 text-center" @click.stop="openPoster(draw)">
                                <view class="mt-[2rpx] mb-[4rpx]">
                                    <u-icon name="photo" size="42"></u-icon>
                                </view>
                                <view class="text-[22rpx] text-content">生成海报</view>
                            </view>
                        </view>
                        <u-button
                            type="primary"
                            size="default"
                            :customStyle="{
                            padding: '0 30rpx',
                            height: '82rpx',
                            width: '300rpx',
                            margin: '0'
                        }"
                        >
                            长按图片保存
                        </u-button>
                    </view>
                </view>
            </view>

            <draw-poster v-if="posterShow" ref="posterRef" @close="posterShow = false"/>
        </view>

        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </page-status>
</template>
<script setup lang="ts">
import {ref, computed, reactive, nextTick, shallowRef} from 'vue'
import {onLoad} from '@dcloudio/uni-app'
import {useCopy} from '@/hooks/useCopy'
import {useRoute, useRouter} from 'uniapp-router-next'
import {useUserStore} from '@/stores/user'
import {DrawRecordItem, ComplexParams, drawRecordDetail} from '@/api/draw'
import {drawSquareCollect, getDrawDetail, getDrawSquareDetail} from "@/api/square";
import {PageStatusEnum} from "@/enums/appEnums";
import DrawPoster from '@/packages/components/draw-poster/draw-poster.vue'
import config from '@/config'
const domain = config.baseUrl

const DrawResultTypeEnum = {
    1: '文生图',
    2: '图生图',
    3: '选中放大',
    4: '选中变换'
}

const {copy} = useCopy()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const status = ref(PageStatusEnum.LOADING)
const draw = ref<DrawRecordItem>({
    id: -1,
    task_id: '',
    prompt: '',
    prompt_en: '',
    prompt_desc: '',
    prompt_other: '',
    status: 3,
    image: '',
    image_base: '',
    thumbnail: '',
    model: '',
    nickname: '',
    image_url: '',
    image_id: -1,
    scale: '',
    able_actions: null,
    fail_reason: '',
    negative_prompt: '',
    version: '',
    style: '',
    engine: '',
    quality: '',
    censor_status: -1,
    create_time: '',
    loras: [],
    type: 1,
    complex_params: ''
})
const collectData = reactive({
    id: ''
})
//海报弹框ref
const posterRef = shallowRef()
const posterShow = ref(false)

const complexParams = computed<ComplexParams>(() => {
    if (draw.value?.complex_params?.length) {
        return JSON.parse(draw.value?.complex_params)
    }
    return {}
})

const handlePraise = async () => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    try {
        await drawSquareCollect({
            records_id: collectData.id,
            status: draw.value.is_collect ? 0 : 1
        })
        draw.value.is_collect = draw.value.is_collect ? 0 : 1
    } catch (e) {
        console.error(e)
        uni.$u.toast(JSON.stringify(e))
    }
}

const openPoster = async (row: any) => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    posterShow.value = true
    // row.records_id = collectData.id
    //#ifndef MP
    await nextTick()
    posterRef?.value?.open(row)
    //#endif
    //#ifdef MP
    setTimeout(() => {
        posterRef.value.open(row)
    }, 300)
    //#endif
}

const getDetail = async (id: number) => {
    try {
        if (route.query.collectId) {
            const data = await getDrawSquareDetail({
                id: id
            })
            data.loras = JSON.parse(data.loras)
            draw.value = data
        } else {
            const data = await drawRecordDetail({
                id: id
            })
            data.loras = JSON.parse(data.loras)
            draw.value = data
        }
        status.value = PageStatusEnum.NORMAL
    } catch (e) {
        status.value = PageStatusEnum.ERROR
    }
}

onLoad(async () => {
    const id = route.query.id
    await getDetail(id as number)
    collectData.id = route.query.collectId as any
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>

<style lang="scss" scoped>
.draw-detail-footer {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    border-radius: 20rpx 20rpx 0 0;
    height: calc(120rpx + env(safe-area-inset-bottom));
    padding: 0 60rpx env(safe-area-inset-bottom) 60rpx;
    background: #F6F6F6;
    box-shadow: 0 -3px 16px 0 rgba(219, 219, 244, 0.102);
}


.praise {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    position: relative;
    width: 42rpx;
    height: 42rpx;
    border-radius: 30px;
}

.praise-animate {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 54px;
    height: 54px;
    background-repeat: no-repeat;
    background-position: left;
    background-size: cover;
}

// 没点赞
.praise-leave {
    background-position: left;
}

// 点赞
.praise-entry {
    background-position: right;
    transition: background 1s steps(28);
}
</style>