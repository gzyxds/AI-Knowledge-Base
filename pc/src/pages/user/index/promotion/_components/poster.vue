<template>
    <div class="poster-container">
        <div class="inline-block" @click="showPopup = true">
            <slot name="trigger" />
        </div>
        <ElDialog v-model="showPopup" title="分销海报" show-close class="!rounded-[15px]" width="390px">
            <div
                style="height: 548px"
                class="flex justify-center overflow-hidden"
            >
                <div
                    v-if="!loading"
                    ref="posterRef"
                    class="poster h-[548px] overflow-hidden"
                >
                    <div class="poster-bg flex flex-col">
                        <img class="w-full min-h-[548px] rounded-[10px]" :src="getPosterBg" alt="" />
                    </div>

                    <div class="w-full h-full poster-contain1">
                        <div
                            class="absolute z-10 bg-white rounded-[10px] p-[5px]"
                            :style="{
                                top: `${drawOptions?.code?.y * 1.218}px`,
                                left: `${drawOptions?.code?.x* 1.218}px`
                            }"
                        >
                            <QrcodeVue
                                :value="inviteLink"
                                :size="110"
                                :margin="1"
                            />
                        </div>

                        <span
                            v-if="drawOptions.showData"
                            class="text-white text-xl absolute z-10"
                            :style="{
                                top: `${drawOptions?.data?.y * 1.218}px`,
                                left: `${drawOptions?.data?.x * 1.218}px`
                            }"
                        >
                            {{ drawOptions?.data?.content }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 px-5 grid grid-cols-2 gap-x-[10px]">
                <div class="flex-1">
                    <el-button class="w-full" @click="copy(pcLink)">
                        <template #icon>
                            <Icon name="el-icon-DocumentCopy" />
                        </template>
                        复制链接
                    </el-button>
                </div>
                <div class="flex-1">
                    <el-button type="primary" class="w-full" :loading="downloadLoading" @click="download">
                        <template #icon>
                            <Icon name="el-icon-Download" />
                        </template>
                        下载
                    </el-button>
                </div>
            </div>
        </ElDialog>
    </div>
</template>
<script lang="ts" setup>
import { getDecorate } from '~/api/app'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import QrcodeVue from 'qrcode.vue'
const appStore = useAppStore()
const { getImageUrl, config } = appStore
const showPopup = ref(false)
const downloadLoading = ref(false)
const drawOptions = ref<any>({})
const loading = ref(false)
const userStore = useUserStore()
const posterRef = shallowRef<HTMLDivElement>()

const getData = async () => {
    loading.value = true
    try {
        const { data } = await getDecorate({ id: 9 })
        drawOptions.value = JSON.parse(data)[0]?.content || {}
    } finally {
        loading.value = false
    }
}

const inviteLink = computed(
    () => `${window.origin}/mobile?user_sn=${userStore.userInfo.sn}`
)

const getPosterBg = computed(() => {
    const data = drawOptions.value
    if (data.default == 1 && data.poster == 1) {
        // 默认海报1
        return getImageUrl(data.defaultUrl1)
    } else if (data.default == 1 && data.poster == 2) {
        // 默认海报2
        return getImageUrl(data.defaultUrl2)
    } else if (data.default == 2) {
        // 自定义海报
        return getImageUrl(data.posterUrl)
    }
})
const { copy } = useCopy()
const pcLink = computed(
    () => `${window.origin}/?user_sn=${userStore.userInfo.sn}`
)

const download = async () => {
    try {
        downloadLoading.value = true
        await downloadHtml2Image(posterRef.value)
    } catch (error) {
        feedback.msgError('下载失败，请重试')
    } finally {
        downloadLoading.value = false
    }
}

onMounted(() => { getData() })
</script>

<style lang="scss" scoped>
.poster-container {
    :deep() {
        .el-dialog__body {
            padding: 0 !important;
        }
    }

    .poster {
        width: 328.86px;
        position: relative;
        overflow: hidden;
        transform-origin: center top;
        &-bg {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        background: transparent;
    }

    .poster-contain1 {
        position: relative;
        z-index: 1;
        background-color: transparent;
    }
}
</style>
