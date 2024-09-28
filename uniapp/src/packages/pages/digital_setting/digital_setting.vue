<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col bg-white">
        <view class="flex-1 min-h-0">
            <scroll-view class="h-full" scroll-y>
                <view class="px-[20rpx]">
                    <u-form
                        ref="uFormRef"
                        :rules="rules"
                        :model="formData"
                        label-position="top"
                        :border-bottom="false"
                    >
                        <u-form-item label="形象名称" prop="name" required>
                            <view class="flex-1 min-w-0">
                                <u-input
                                    v-model="formData.name"
                                    placeholder="请输入形象名称"
                                    :border="true"
                                />
                            </view>
                        </u-form-item>
                        <u-form-item label="形象头像" prop="avatar" required>
                            <view class="flex-1 min-w-0">
                                <app-upload v-model="formData.avatar" />
                                <view class="text-muted">
                                    建议尺寸：50*50px
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item label="形象封面" prop="image" required>
                            <view class="flex-1 min-w-0">
                                <app-upload v-model="formData.image" />
                                <view class="text-muted">
                                    建议尺寸：280*50px
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item
                            label="宽屏人物待机视频"
                            prop="wide_stay_video"
                            required
                        >
                            <view class="flex-1 min-w-0">
                                <app-upload
                                    accept="video"
                                    v-model="formData.wide_stay_video"
                                />
                                <view class="text-muted">
                                    格式为MP4，大小不能超过20M
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item
                            label="宽屏人物说话视频"
                            prop="wide_talk_video"
                            required
                        >
                            <view class="flex-1 min-w-0">
                                <app-upload
                                    accept="video"
                                    v-model="formData.wide_talk_video"
                                />
                                <view class="text-muted">
                                    格式为MP4，大小不能超过20M
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item
                            label="竖屏人物待机视频"
                            prop="vertical_stay_video"
                            required
                        >
                            <view class="flex-1 min-w-0">
                                <app-upload
                                    accept="video"
                                    v-model="formData.vertical_stay_video"
                                />
                                <view class="text-muted">
                                    格式为MP4，大小不能超过20M
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item
                            label="竖屏人物说话视频"
                            prop="vertical_talk_video"
                            required
                        >
                            <view class="flex-1 min-w-0">
                                <app-upload
                                    accept="video"
                                    v-model="formData.vertical_talk_video"
                                />
                                <view class="text-muted">
                                    格式为MP4，大小不能超过20M
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item label="选择配音" prop="dubbing" required>
                            <view class="flex-1 min-w-0">
                                <dub-picker v-model="formData.dubbing" />
                            </view>
                        </u-form-item>
                        <u-form-item
                            label="自定义闲时时间"
                            prop="idle_time"
                            required
                        >
                            <view class="flex-1 min-w-0">
                                <view
                                    class="flex border border-solid border-light rounded-[8rpx] pl-[20rpx]"
                                >
                                    <view class="flex-1 min-w-0">
                                        <u-input
                                            type="number"
                                            v-model="formData.idle_time"
                                            placeholder="请输入自定义闲时时间"
                                        />
                                    </view>
                                    <view
                                        class="bg-[#e5e5e5] px-[25rpx] flex items-center text-content"
                                    >
                                        秒
                                    </view>
                                </view>
                                <view class="text-muted">
                                    例如：选择5s，每隔5秒就会有一个回复内容，内容是在
                                    闲时回复内容的文案
                                </view>
                            </view>
                        </u-form-item>
                        <u-form-item
                            label="闲时回复内容"
                            prop="idle_reply"
                            required
                        >
                            <view class="flex-1 min-w-0">
                                <u-input
                                    v-model="formData.idle_reply"
                                    type="textarea"
                                    :height="150"
                                    placeholder="请输入内容"
                                    :border="true"
                                />
                                <view class="text-muted">
                                    根据自定义闲时时间段设置后形象回复的内容
                                </view>
                            </view>
                        </u-form-item>
                    </u-form>
                </view>
            </scroll-view>
        </view>
        <view class="p-[20rpx]">
            <u-button type="primary" @click="handelSave"> 确定 </u-button>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'uniapp-router-next'
import { getDigitalDetail, putDigital } from '@/api/digital'
import DubPicker from '@/packages/components/dub-picker/dub-picker.vue'

const router = useRouter()
const route = useRoute()

const uFormRef = shallowRef()
const formData = ref({
    name: '',
    avatar: '',
    image: '',
    wide_stay_video: '',
    wide_talk_video: '',
    vertical_stay_video: '',
    vertical_talk_video: '',
    channel: '',
    dubbing: '',
    idle_time: 10,
    idle_reply: ''
})
const rules = {
    name: [
        {
            required: true,
            message: '请输入形象名称'
        }
    ],
    avatar: [
        {
            required: true,
            type: 'string',
            message: '请选择形象头像'
        }
    ],
    image: [
        {
            required: true,
            type: 'string',
            message: '请选择形象封面'
        }
    ],
    wide_stay_video: [
        {
            required: true,
            type: 'string',
            message: '请选择宽屏人物待机视频'
        }
    ],
    wide_talk_video: [
        {
            required: true,
            type: 'string',
            message: '请选择宽屏人物说话视频'
        }
    ],
    vertical_stay_video: [
        {
            required: true,
            type: 'string',
            message: '请选择竖屏人物待机视频'
        }
    ],
    vertical_talk_video: [
        {
            required: true,
            type: 'string',
            message: '请选择竖屏人物说话视频'
        }
    ],
    dubbing: [
        {
            required: true,
            message: '请选择配音角色'
        }
    ],
    idle_time: [
        {
            required: true,
            message: '请输入自定义闲时时间'
        }
    ],
    idle_reply: [
        {
            required: true,
            type: 'string',
            message: '请输入闲时回复内容'
        }
    ]
}

const handelSave = () => {
    uFormRef.value.validate(async (valid: boolean) => {
        if (valid) {
            formData.value.idle_time = Number(formData.value.idle_time)
            await putDigital(formData.value)
            setTimeout(() => {
                router.navigateBack()
            }, 1000)
        }
    })
}
const getData = async () => {
    formData.value = await getDigitalDetail({
        id: route.query.id
    })
}
onMounted(() => {
    getData()
    setTimeout(() => {
        uFormRef.value.setRules(rules)
    }, 10)
})
</script>
<style lang="scss">
page {
    height: 100%;
}
.tab-active {
    background: linear-gradient(
        90deg,
        var(--color-minor) 0%,
        var(--color-primary) 100%
    );
    @apply text-white;
}
</style>
