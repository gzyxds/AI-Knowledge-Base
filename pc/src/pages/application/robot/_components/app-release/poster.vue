<template>
    <ElDialog v-model="showModel" title="生成海报" width="400">
        <div class="poster relative" ref="posterRef">
            <img v-if="bgUrl" class="object-cover w-full h-full" :src="bgUrl" />
            <div
                class="absolute top-0 left-0 w-full h-full flex flex-col justify-center items-center"
            >
                <VueQr
                    :text="link"
                    size="200"
                    class="rounded-[10px]"
                    dotScale="1"
                    margin="12"
                />
                <div
                    class="text-center text-white mt-[15px] text-[18px] font-bold"
                >
                    <div>{{ title }}</div>
                    <div>{{ description }}</div>
                </div>
            </div>
        </div>
        <div class="flex py-3 ml-[-5px] items-center">
            <Upload :limit="1" @success="uploadFileSuccess">
                <el-button type="primary" link>自定义背景图</el-button>
            </Upload>
            <div class="flex-1 ml-3">
                <el-button type="primary" link @click="useDefaultBg"
                    >使用默认图</el-button
                >
            </div>
            <div class="text-tx-regular">背景图尺寸：430*670</div>
        </div>
        <div class="">
            <div class="flex items-center">
              <div class="text-tx-regular flex-none mr-2">标题</div>
              <el-input v-model="title" placeholder="请输入背景图地址" />
            </div>
            <div class="flex items-center">
                <div class="text-tx-regular flex-none mr-2">描述</div>
                <el-input v-model="description" placeholder="请输入背景图地址" class="py-3" />
            </div>
        </div>
        <div>
            <el-button
                type="primary"
                size="large"
                class="w-full"
                :loading="isLock"
                @click="download"
            >
                保存
            </el-button>
        </div>
    </ElDialog>
</template>

<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import { putReleaseSetBg } from '@/api/robot'
import VueQr from 'vue-qr/src/packages/vue-qr.vue'
import { baseUrl } from '@/config'
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
const defaultBg = `${baseUrl}/resource/image/other/ai_share_bg.png`
const bgUrl = ref(defaultBg)
const title = ref<string>('快来扫码')
const description = ref<string>('和我的智能体对话吧')

const uploadFileSuccess = (res: any) => {
    bgUrl.value = res.uri
}
const posterRef = shallowRef()
const link = computed(() => `${location.origin}/chat/${props.apikey}`)
const { lockFn: download, isLock } = useLockFn(async () => {
    try {
        await putReleaseSetBg({
            id: props.shareId,
            url: bgUrl.value
        })
        emit('update')
        await downloadHtml2Image(posterRef.value)
    } catch (error) {
        feedback.msgError('下载失败，请重试')
        return Promise.reject()
    }
})

const useDefaultBg = () => {
    bgUrl.value = defaultBg
}
watch(
    () => props.url,
    (value) => {
        if (!value) {
            bgUrl.value = defaultBg
        } else {
            bgUrl.value = value
        }
    }
)
</script>

<style lang="scss" scoped>
.poster {
    width: 100%;
    height: 560px;
    border-radius: 10px;
    overflow: hidden;
}
</style>