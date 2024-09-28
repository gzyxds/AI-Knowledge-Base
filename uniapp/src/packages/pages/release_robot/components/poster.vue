<template>
    <u-popup v-model="showModel" mode="center" closeable border-radius="14">
        <view class="px-[40rpx]">
            <view class="py-[30rpx]"> 生成海报</view>
            // #ifdef H5
            <view style="height: 700rpx;">
                <view style="transform: scale(0.8) translateY(-14%);">
            // #endif
                    <l-painter
                        ref="painterRef"
                        :isCanvasToTempFilePath="false"
                        :after-delay="100"
                        :css="`width: 570rpx;    
                        height: 888rpx; 
                        `"
                    >
                        <l-painter-view
                            :css="`width: 570rpx;     
                            display: flex;
                            box-sizing: border-box;
                            border-radius: 14rpx;
                            height: 888rpx; 
                            background-image: url(${bgUrl});
                            justify-content:center;
                            align-items:center;
                            `"
                        >
                            <l-painter-view>
                                <l-painter-view
                                    :css="`
                                        text-align: center;
                                    `"
                                >
                                    <l-painter-qrcode
                                        :css="`
                                        width:280rpx;
                                        height:280rpx;
                                        box-sizing: border-box;
                                        padding: 20rpx;
                                        background-color: #FFFFFF;
                                        border-radius:20rpx
                                    `"
                                        :text="link"
                                    >
                                    </l-painter-qrcode>
                                </l-painter-view>
                                <l-painter-view
                                    :css="`
                                        text-align: center;
                                        margin-top: 30rpx
                                    `"
                                >
                                    <l-painter-text
                                        :text="title"
                                        :css="`
                                            color: #FFFFFF;
                                            text-align: center;
                                            font-size: 32rpx;
                                            font-weight: bold;
                                        `"
                                    />
                                </l-painter-view>
                                <l-painter-view>
                                    <l-painter-text
                                        :text="description"
                                        :css="`
                                            color: #FFFFFF;
                                            text-align: center;
                                            font-size: 32rpx;
                                            font-weight: bold;
                                        `"
                                    />
                                </l-painter-view>
                            </l-painter-view>
                        </l-painter-view>
                    </l-painter>
            // #ifdef H5
                </view>
            </view>
            // #endif
            
            <view class="flex py-3 items-center">
                <file-upload
                    returnType="object"
                    :maxCount="1"
                    fileType="image"
                    :showFilesLists="false"
                    @update:modelValue="uploadFileChange"
                >
                    <view class="text-primary">自定义背景图</view>
                </file-upload>
                <view class="flex-1 ml-[20rpx]">
                    <view class="text-primary" @click="useDefaultBg">
                        使用默认图
                    </view>
                </view>
                <view class="text-muted text-xs">尺寸：430*670</view>
            </view>
            <view class="">
                <view class="flex items-center">
                    <view class="text-tx-regular flex-none mr-2">标题</view>
                    <view class="flex-1">
                        <u-input v-model="title" placeholder="请输入背景图地址" border />
                    </view>
                </view>
                <view class="flex items-center">
                    <view class="text-tx-regular flex-none mr-2">描述</view>
                    <view class="flex-1 py-3">
                        <u-input v-model="description" placeholder="请输入背景图地址" border />
                    </view>
                      
                </view>
            </view>

            <view class="pb-[30rpx]">
                <u-button
                    type="primary"
                    class="w-full"
                    :loading="isLock"
                    @click="handelSave"
                >
                    保存
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import { putReleaseSetBg } from '@/api/robot'
import config from '@/config'
import { computed, ref, shallowRef, watch } from 'vue'
import { useLockFn } from '@/hooks/useLockFn'
import { download } from '@/utils/download'
import { isDevMode } from '@/utils/env'
import { saveImageToPhotosAlbum } from '@/utils/file'
const props = defineProps<{
    show: boolean
    url: string
    apikey: string
    shareId: string
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
    (event: 'update'): void
}>()
const showModel = useVModel(props, 'show', emit)
const defaultBg = computed(() => {
    const uri = 'resource/image/other/ai_share_bg.png'
    if (isDevMode()) {
        return `${config.baseUrl}${uri}`
    } else {
        return `${window.location.origin}/${uri}`
    }
})
const bgUrl = ref(defaultBg.value)

const title = ref<string>('快来扫码')
const description = ref<string>('和我的智能体对话吧')

const uploadFileChange = (res: any) => {
    if (res.url) {
        bgUrl.value = res.url
    }
}

const link = computed(() => {
    let origin = config.baseUrl
    //#ifdef H5
    origin = `${location.origin}/`
    //#endif
    return `${origin}chat/${props.apikey}`
})
const { lockFn: handelSave, isLock } = useLockFn(async () => {
    try {
        await putReleaseSetBg({
            id: props.shareId,
            url: bgUrl.value
        })
        emit('update')

        const path = await drawCanvas()
        //#ifdef H5
        download(path, '海报')
        //#endif
        //#ifndef H5
        console.log(path)
        saveImageToPhotosAlbum(path, { download: false })
        //#endif
    } catch (error) {
        return Promise.reject()
    }
})

const useDefaultBg = () => {
    bgUrl.value = defaultBg.value
}
const painterRef = ref()

const drawCanvas = (): Promise<string> => {
    return new Promise((resolve, reject) => {
        try {
            // 生成图片
            painterRef.value?.canvasToTempFilePathSync({
                fileType: 'png',
                pathType: 'url',
                quality: 1,
                success: (res: any) => {
                    console.log(res)
                    resolve(res.tempFilePath)
                },
                fail: (error: any) => {
                    reject(error)
                    uni.hideLoading()
                    uni.$u.toast('调用海报错误', error)
                }
            })
        } catch (error) {
            console.log(error)
            reject(error)
            uni.hideLoading()
            uni.$u.toast('调用海报错误')
        }
    })
}

watch(
    () => props.url,
    (value) => {
        if (!value) {
            bgUrl.value = defaultBg.value
        } else {
            bgUrl.value = value
        }
    }
)
</script>

<style lang="scss" scoped>
// .poster {
//     width: 100%;
//     height: 560px;
//     border-radius: 10px;
//     overflow: hidden;
// }
</style>
