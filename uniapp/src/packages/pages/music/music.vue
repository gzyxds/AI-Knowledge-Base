<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col">
        <u-navbar back-text="音乐">
            <template #right>
                <router-navigate to="/packages/pages/music_list/music_list">
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
                    v-for="item in musicModeList"
                    :key="item.mode"
                    class="flex items-center px-[10rpx] py-[5rpx] flex-1 justify-center text-primary rounded-full"
                    :class="{
                        '!bg-primary !text-white':
                            formData.custom_mode === item.mode
                    }"
                    @click="formData.custom_mode = item.mode"
                >
                    <view>{{ item.name }}</view>
                    <view
                        class="flex p-[16rpx]"
                        @click.stop="showTips(item.name, item.tips)"
                    >
                        <u-icon name="error-circle" :size="32" />
                    </view>
                </view>
            </view>
        </view>
        <view class="flex-1 min-h-0">
            <scroll-view class="h-full" scroll-y>
                <view class="px-[20rpx]">
                    <u-form
                        ref="uFormRef"
                        :model="formData"
                        label-position="top"
                        :border-bottom="false"
                    >
                        <u-form-item label="歌曲名称" prop="title" required>
                            <view class="flex-1 min-w-0">
                                <u-input
                                    placeholder-style="color: #9CA3AF;"
                                    :height="80"
                                    v-model="formData.title"
                                    placeholder="给你的歌曲起个好听的名字"
                                    :border="true"
                                    @click="handleLogin"
                                />
                            </view>
                        </u-form-item>
                        <u-form-item
                            :label="isMuseMode ? '歌曲描述' : '歌词'"
                            label-position="left"
                            required
                            :label-width="isMuseMode ? 250 : 70"
                        >
                            <view class="flex flex-1">
                                <view
                                    v-if="!isMuseMode"
                                    class="flex"
                                    @click.stop="
                                        showTips(
                                            '温馨提示',
                                            '建议歌词长度100-1250，太长或太短容易生成失败'
                                        )
                                    "
                                >
                                    <u-icon name="error-circle" :size="32" />
                                </view>
                                <view
                                    class="flex items-center text-content text-xs ml-auto"
                                    @click.stop="formData.prompt = ''"
                                >
                                    <u-icon name="trash" />
                                    <view class="ml-[8rpx]"> 清空 </view>
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item>
                            <view
                                class="flex-1 min-w-0 overflow-hidden mt-[-25rpx]"
                            >
                                <l-textarea
                                    v-model="formData.prompt"
                                    maxlength="1300"
                                    :rows="4"
                                    :show-word-limit="true"
                                    :custom-class="{
                                        background: '#fff',
                                        paddingBottom: '70rpx',
                                        fontSize: '28rpx'
                                    }"
                                    :placeholder="`请输入${
                                        isMuseMode
                                            ? '灵感，，例如写一首关于夏天的歌曲'
                                            : '歌词'
                                    }`"
                                    @click="handleLogin"
                                >
                                    <template #length-suffix>
                                        <view class="flex length-suffix">
                                            <view class="flex-1">
                                                <view
                                                    v-if="isShowImagine"
                                                    class="inline-flex items-center text-primary bg-primary-light-9 rounded-[10rpx] px-[15rpx] leading-[50rpx]"
                                                    @click.stop="handelImagine"
                                                >
                                                    <u-loading
                                                        v-if="isLock"
                                                        mode="flower"
                                                        :color="
                                                            $theme.primaryColor
                                                        "
                                                        size="24"
                                                    ></u-loading>
                                                    <image
                                                        v-else
                                                        class="w-[24rpx] h-[24rpx]"
                                                        src="@/static/images/icon/icon_light.png"
                                                    ></image>
                                                    <text class="ml-[8rpx]">
                                                        <text> 智能联想 </text>

                                                        <text
                                                            v-if="
                                                                musicConfig
                                                                    .imagine
                                                                    .price > 0
                                                            "
                                                            >：消耗{{
                                                                musicConfig
                                                                    .imagine
                                                                    .price
                                                            }}{{
                                                                appStore.getTokenUnit
                                                            }}
                                                        </text>
                                                    </text>
                                                </view>
                                            </view>
                                        </view>
                                    </template>
                                </l-textarea>
                            </view>
                        </u-form-item>
                        <u-form-item prop="version" label="选择版本" required>
                            <div class="flex flex-wrap">
                                <view
                                    v-for="(
                                        item, value
                                    ) in currentModel.version"
                                    :key="item"
                                    class="mr-[20rpx] h-[70rpx] leading-[70rpx] px-[40rpx] rounded-[8rpx] border-solid border-light border-[1px]"
                                    :class="{
                                        '!text-primary !border-primary':
                                            formData.version === String(value)
                                    }"
                                    @click="formData.version = String(value)"
                                >
                                    {{ item }}
                                </view>
                            </div>
                        </u-form-item>
                        <u-form-item
                            v-if="isMuseMode"
                            label="纯音乐"
                            label-position="left"
                            label-width="150"
                        >
                            <view class="flex justify-end flex-1">
                                <u-switch
                                    v-model="formData.make_instrumental"
                                ></u-switch>
                            </view>
                        </u-form-item>
                        <template v-if="!isMuseMode">
                            <u-form-item
                                label="音乐风格"
                                label-position="left"
                                label-width="200"
                            >
                                <view class="flex-1 min-w-0 flex justify-end">
                                    <view
                                        class="text-primary"
                                        @click="changeStyleCustom"
                                    >
                                        {{
                                            formData.is_style_custom
                                                ? '系统风格'
                                                : '自定义风格'
                                        }}
                                    </view>
                                </view>
                            </u-form-item>
                            <u-form-item class="mt-[-15px]">
                                <view
                                    class="flex-1 min-w-0 bg-white rounded-[16rpx]"
                                    v-if="!formData.is_style_custom"
                                >
                                    <swiper
                                        class="h-[520rpx]"
                                        circular
                                        :indicator-dots="swiperNum > 1"
                                        :indicator-active-color="
                                            $theme.primaryColor
                                        "
                                    >
                                        <swiper-item
                                            v-for="(i, index) in swiperNum"
                                            :key="index"
                                        >
                                            <view
                                                class="flex flex-wrap px-[20rpx] pt-[20rpx] mx-[-10rpx]"
                                            >
                                                <view
                                                    v-for="item in sliceStyle(
                                                        index
                                                    )"
                                                    class="w-[25%] px-[10rpx] mb-[10rpx]"
                                                    :key="item.id"
                                                >
                                                    <view
                                                        class="h-full cursor-pointer"
                                                        @click="
                                                            selectStyle(item.id)
                                                        "
                                                    >
                                                        <view
                                                            class="pt-[100%] relative h-0 rounded-[10rpx] overflow-hidden"
                                                        >
                                                            <view
                                                                class="absolute inset-0 left-0 top-0"
                                                            >
                                                                <u-image
                                                                    :src="
                                                                        item.image
                                                                    "
                                                                    class="h-full w-full"
                                                                    width="100%"
                                                                    height="100%"
                                                                />
                                                            </view>
                                                            <view
                                                                v-if="
                                                                    formData.style_id.includes(
                                                                        item.id
                                                                    )
                                                                "
                                                                class="absolute bg-[rgba(0,0,0,0.5)] inset-0 left-0 top-0 flex items-center justify-center text-white"
                                                            >
                                                                <u-icon
                                                                    name="checkmark-circle-fill"
                                                                    :size="40"
                                                                />
                                                            </view>
                                                        </view>
                                                        <view
                                                            class="text-center text-xs"
                                                        >
                                                            {{ item.name }}
                                                        </view>
                                                    </view>
                                                </view>
                                            </view>
                                        </swiper-item>
                                    </swiper>
                                </view>
                                <view
                                    class="flex-1 min-w-0 overflow-hidden"
                                    v-else
                                >
                                    <l-textarea
                                        v-model="formData.style_custom"
                                        maxlength="1024"
                                        :rows="4"
                                        :show-word-limit="false"
                                        :custom-class="{
                                            background: '#fff',
                                            paddingBottom: '70rpx',
                                            fontSize: '28rpx'
                                        }"
                                        placeholder="请输入自定义风格，多个风格以,隔开"
                                    >
                                    </l-textarea>
                                </view>
                            </u-form-item>
                        </template>
                    </u-form>
                </view>
            </scroll-view>
        </view>
        <view class="p-[20rpx] bg-white">
            <u-button
                type="primary"
                :loading="isLockGenerate"
                @click="handelMusicGenerate"
            >
                <view>
                    <text class="text-xl font-bold">立即生成</text>
                    <text class="text-sm ml-[4px]" v-if="musicConfig.is_member">
                        会员免费
                    </text>
                    <text
                        class="text-sm ml-[4px]"
                        v-else-if="currentModel.price > 0"
                    >
                        消耗 {{ currentModel.price }}
                        {{ appStore.getTokenUnit }}
                    </text>
                </view>
            </u-button>
        </view>
        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { computed, reactive, ref, shallowRef } from 'vue'
import {
    getMusicConfig,
    postMusicImagine,
    postMusicGenerate
} from '@/api/music'
import { useRoute, useRouter } from 'uniapp-router-next'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { useLockFn } from '@/hooks/useLockFn'
import { onShow } from '@dcloudio/uni-app'

const router = useRouter()
const route = useRoute()
const uFormRef = shallowRef()
const appStore = useAppStore()
const userStore = useUserStore()
enum MusicMode {
    MUSE = 0,
    LYRIC = 1
}

const musicModeList = ref([
    {
        mode: MusicMode.MUSE,
        name: '灵感模式',
        tips: '只需输入一段歌曲描述，即可生成一首歌'
    },
    {
        mode: MusicMode.LYRIC,
        name: '歌词模式',
        tips: '输入自己的歌词，即可生成一首歌'
    }
])

const formData = reactive({
    channel: 'go_api',
    custom_mode: MusicMode.MUSE,
    title: '',
    prompt: '',
    style_id: [] as number[],
    is_style_custom: 0,
    style_custom: '',
    make_instrumental: 0,
    version: ''
})
const isMuseMode = computed(() => formData.custom_mode === MusicMode.MUSE)
const sliceNum = ref(8)
const sliceStyle = (index: number) => {
    return musicConfig.value.style.slice(
        sliceNum.value * index,
        sliceNum.value * (index + 1)
    )
}

const swiperNum = computed(() => {
    return Math.ceil(musicConfig.value.style.length / sliceNum.value)
})

const selectStyle = (id: number) => {
    const index = formData.style_id.findIndex((item: any) => item === id)
    if (index !== -1) {
        formData.style_id.splice(index, 1)
    } else {
        formData.style_id.push(id)
    }
}

const handleLogin = () => {
    if (userStore.isLogin) return
    router.navigateTo('/pages/login/login')
}

const changeStyleCustom = () => {
    formData.is_style_custom = formData.is_style_custom ? 0 : 1
}

const showTips = (title: string, content: string) => {
    uni.showModal({
        title,
        content,
        showCancel: false
    })
}

const musicConfig = ref<any>({
    model: {},
    style: [],
    imagine: {}
})

const getData = async () => {
    musicConfig.value = await getMusicConfig()
    formData.channel = musicConfig.value.channel
    if (!formData.version) {
        formData.version = Object.keys(currentModel.value.version)[0] || ''
    }
}

const isShowImagine = computed(() => {
    return !isMuseMode.value && musicConfig.value.imagine.status
})

const currentModel = computed<any>(() => {
    return musicConfig.value.model[formData.channel] || {}
})
const { lockFn: handelImagine, isLock } = useLockFn(async () => {
    if (!formData.title) return uni.$u.toast('请输入歌曲名称')
    const data = await postMusicImagine({
        prompt: formData.title
    })
    formData.prompt = data.content
})

const { lockFn: handelMusicGenerate, isLock: isLockGenerate } = useLockFn(
    async () => {
        try {
            if (!formData.title) return uni.$u.toast('请输入歌曲名称')
            if (!formData.prompt)
                return uni.$u.toast(
                    `请输入${!isMuseMode.value ? '歌词' : '歌曲描述'}`
                )
            await postMusicGenerate({
                ...formData,
                style_id: formData.is_style_custom ? [] : formData.style_id
            })
            formData.prompt = ''
            formData.title = ''
            formData.style_id = []
            formData.style_custom = ''
            router.navigateTo('/packages/pages/music_list/music_list')
        } catch (error) {
        } finally {
        }
    }
)
onShow(() => {
    getData()
})
</script>
<style lang="scss">
page {
    height: 100%;
    overflow: hidden;
}
.u-input {
    background-color: #fff;
    border-color: transparent !important;
    &:focus {
        border-color: $u-type-primary !important;
    }
}
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
