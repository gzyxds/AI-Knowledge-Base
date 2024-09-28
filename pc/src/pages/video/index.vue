<template>
    <div>
        <NuxtLayout name="default">
            <div class="h-full p-[16px] flex">
                <div
                    class="bg-body w-[355px] h-full rounded-[12px] flex flex-col"
                >
                    <div class="p-4">
                        <VideoType v-model="formData.type" />
                    </div>
                    <div class="flex-1 min-h-0">
                        <el-scrollbar>
                            <el-form
                                class="px-4"
                                ref="formRef"
                                :model="formData"
                                label-position="top"
                                :show-message="false"
                            >
                                <UploaderPicture
                                    v-model="formData.image"
                                    v-if="formData.type === 2"
                                />
                                <Prompt
                                    :type="formData.type"
                                    v-model="formData.prompt"
                                    :config="videoConfig.example"
                                    :showTranslate="
                                        !!videoConfig.translate_switch
                                    "
                                />
                                <VideoSize v-model="formData.scale" />
                                <VideoStyle
                                    v-if="videoConfig.style.length"
                                    :style-list="videoConfig.style"
                                    v-model="formData.style_id"
                                />
                            </el-form>
                        </el-scrollbar>
                    </div>
                    <div class="p-4">
                        <el-button
                            size="large"
                            class="w-full"
                            type="primary"
                            :loading="isLockGenerate"
                            @click="handelVideoGenerate"
                        >
                            <div>
                                <span class="text-base font-bold">立即生成</span>
                                <span
                                    class="text-sm ml-[4px]"
                                    v-if="videoConfig.is_member"
                                >
                                    会员免费
                                </span>
                                <span
                                    class="text-sm ml-[4px]"
                                    v-else-if="currentModel.price > 0"
                                >
                                    消耗 {{ currentModel.price }}
                                    {{ appStore.getTokenUnit }}
                                </span>
                            </div>
                        </el-button>
                    </div>
                </div>
                <div class="ml-4 flex-1 min-w-0 h-full">
                    <VideoResult
                        ref="videoResultRef"
                        @regenerate="regenerate"
                    />
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import { getVideoConfig, postVideoGenerate } from '@/api/video'
import VideoType from './_components/video-type.vue'
import VideoSize from './_components/video-size.vue'
import Prompt from './_components/prompt.vue'
import VideoStyle from './_components/video-style.vue'
import UploaderPicture from './_components/uploader-picture.vue'
import VideoResult from './_components/video-result.vue'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'

const appStore = useAppStore()
const userStore = useUserStore()
const videoResultRef = shallowRef()
const formData = reactive({
    type: 1,
    prompt: '',
    scale: '1:1',
    image: '',
    style_id: [],
    channel: ''
})

const { data: videoConfig, refresh } = useAsyncData(() => getVideoConfig(), {
    default() {
        return {
            model: {},
            style: [],
            example: {}
        }
    },
    lazy: true
})

watch(videoConfig, (value) => {
    formData.channel = value.channel
})

const regenerate = (item: any) => {
    Object.assign(formData, item)
}

const currentModel = computed<any>(() => {
    return videoConfig.value.model[formData.channel] || {}
})

const { lockFn: handelVideoGenerate, isLock: isLockGenerate } = useLockFn(
    async () => {
        try {
            if (!formData.prompt) {
                feedback.msgError(
                    `请输入${formData.type === 1 ? '视频场景' : '描述词'}`
                )
                return
            }
            if (formData.type === 2 && !formData.image)
                return feedback.msgError('请上传上传参考图')
            await postVideoGenerate({
                ...formData
            })
            formData.prompt = ''
            formData.style_id = []
            videoResultRef.value?.refresh()
            userStore.getUser()
            refresh()
        } catch (error) {
        } finally {
        }
    }
)

definePageMeta({
    layout: false,
    hiddenFooter: true
})
</script>
