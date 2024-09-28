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
        <u-navbar back-text="视频">
            <template #right>
                <router-navigate to="/packages/pages/video_list/video_list">
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
                            Number(formData.type) === item.mode
                    }"
                    @click="formData.type = item.mode"
                >
                    <view class="leading-[30px]">{{ item.name }}</view>
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
                        <uploader-picture
                            v-model="formData.image"
                            v-if="formData.type === VideoMode.IMAGE"
                        />
                        <prompt
                            :type="formData.type"
                            v-model="formData.prompt"
                            :config="videoConfig.example"
                            :showTranslate="!!videoConfig.translate_switch"
                        />
                        <video-size v-model="formData.scale" />
                        <video-style
                            v-if="videoConfig.style.length"
                            :style-list="videoConfig.style"
                            v-model="formData.style_id"
                        />
                    </u-form>
                </view>
            </scroll-view>
        </view>
        <view class="p-[20rpx] bg-white">
            <u-button
                type="primary"
                :loading="isLockGenerate"
                @click="handelVideoGenerate"
            >
                <view>
                    <text class="text-xl font-bold">立即生成</text>
                    <text class="text-sm ml-[4px]" v-if="videoConfig.is_member">
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
import { getVideoConfig, postVideoGenerate } from '@/api/video'
import { useRoute, useRouter } from 'uniapp-router-next'
import { useAppStore } from '@/stores/app'
import { useLockFn } from '@/hooks/useLockFn'
import { onShow, onLoad } from '@dcloudio/uni-app'
import prompt from './components/prompt.vue'
import VideoSize from './components/video-size.vue'
import VideoStyle from './components/video-style.vue'
import UploaderPicture from './components/uploader-picture.vue'

const router = useRouter()
const route = useRoute()
const uFormRef = shallowRef()
const appStore = useAppStore()
enum VideoMode {
    TEXT = 1,
    IMAGE = 2
}

const modeList = ref([
    {
        mode: VideoMode.TEXT,
        name: '文生视频'
    },
    {
        mode: VideoMode.IMAGE,
        name: '图生视频'
    }
])

const formData = reactive({
    type: VideoMode.TEXT,
    prompt: '',
    scale: '1:1',
    image: '',
    style_id: [],
    channel: ''
})

const videoConfig = ref<any>({
    model: {},
    style: [],
    example: {}
})

const getData = async () => {
    videoConfig.value = await getVideoConfig()
    formData.channel = videoConfig.value.channel
}

const currentModel = computed<any>(() => {
    return videoConfig.value.model[formData.channel] || {}
})

const { lockFn: handelVideoGenerate, isLock: isLockGenerate } = useLockFn(
    async () => {
        try {
            if (!formData.prompt) {
                return uni.$u.toast(
                    `请输入${formData.type === 1 ? '视频场景' : '描述词'}`
                )
            }
            if (formData.type === 2 && !formData.image)
                return uni.$u.toast('请上传参考图')
            await postVideoGenerate({
                ...formData
            })
            formData.prompt = ''
            formData.style_id = []
            formData.image = ''
            router.navigateTo('/packages/pages/video_list/video_list')
        } catch (error) {
        } finally {
        }
    }
)

getData()
onLoad(() => {
    uni.$on('videoRegenerate', (data: any) => {
        Object.assign(formData, data)
    })
    const query = route.query
    try {
        const data = JSON.parse(query.data as string)
        Object.assign(formData, data)
    } catch (error) {}
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
</style>
