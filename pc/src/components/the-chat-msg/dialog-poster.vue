<template>
    <ElDialog v-model="showPopup" title="生成海报" show-close width="430px">
        <div class="h-[70vh]">
            <ElScrollbar v-loading="loading">
                <div
                    ref="posterRef"
                    class="poster overflow-hidden pb-[10px] rounded-lg"
                    v-if="!loading"
                >
                    <div class="poster-bg flex flex-col">
                        <img
                            class="w-full"
                            :src="
                                drawOptions.default == 2
                                    ? appStore.getImageUrl(
                                          drawOptions.posterUrl
                                      )
                                    : drawOptions.poster == 1
                                    ? appStore.getImageUrl(
                                          drawOptions.defaultUrl1
                                      )
                                    : appStore.getImageUrl(
                                          drawOptions.defaultUrl2
                                      )
                            "
                            :style="{
                                background: drawOptions.bgColor
                            }"
                            alt=""
                        />
                        <div
                            class="flex-1 min-h-0"
                            :style="{
                                background: drawOptions.bgColor
                            }"
                        ></div>
                    </div>

                    <div
                        class="w-full h-full poster-contain1 bg-[#BBBBBB]"
                        :style="{
                            color: drawOptions.textColor
                        }"
                    >
                        <div class="px-[20px] pt-[135px]">
                            <div
                                class="bg-white rounded-lg p-[15px] text-tx-primary"
                            >
                                <div
                                    class="flex justify-end text-[16px] items-baseline"
                                >
                                    <span
                                        class="bg-[#066cff] px-[10px] py-[6px] text-white"
                                        style="border-radius: 8px 0 8px 8px"
                                    >
                                        <span class="line-clamp-2">
                                            {{ posterData.title }}
                                        </span>
                                    </span>
                                </div>
                                <span class="text-[14px]">
                                    <div
                                        v-for="(
                                            text, index
                                        ) in posterData.content"
                                        :key="index"
                                        class="mb-[15px] mt-4 p-[10px] bg-[#f0f5fe] text-tx-primary"
                                        style="border-radius: 0 8px 8px 8px"
                                        :class="{
                                            'pt-[15px]': index > 0
                                        }"
                                    >
                                        <Markdown
                                            :content="text"
                                            :line-numbers="true"
                                            :line-clamp="
                                                drawOptions.showContentType == 1
                                                    ? drawOptions.contentNum
                                                    : 0
                                            "
                                            theme="light"
                                        />
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="px-[20px] pt-[20px]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img
                                        :src="userStore.userInfo.avatar"
                                        alt=""
                                        class="w-[60px] h-[60px] rounded-full"
                                    />
                                    <div class="ml-[10px] text-[16px]">
                                        <div class="line-clamp-2">
                                            {{ userStore.userInfo.nickname }}
                                        </div>
                                        <div v-if="drawOptions.showData == 1">
                                            {{ drawOptions.data }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="w-[120px] flex flex-col justify-center items-end"
                                >
                                    <QrcodeVue
                                        :value="inviteLink"
                                        :size="85"
                                        :margin="1"
                                    />
                                    <div class="text-xs mt-2">
                                        长按识别二维码
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </ElScrollbar>
        </div>
        <div class="flex justify-end mt-[10px]">
            <el-button round @click="download" :loading="downloadLoading">
                <template #icon>
                    <Icon name="el-icon-Download" />
                </template>
                下载
            </el-button>
            <el-button round @click="copy(pcLink)">
                <template #icon>
                    <Icon name="el-icon-DocumentCopy" />
                </template>
                复制链接
            </el-button>
        </div>
    </ElDialog>
</template>
<script lang="ts" setup>
import { getDecorate } from '~/api/app'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import QrcodeVue from 'qrcode.vue'
const appStore = useAppStore()
const showPopup = ref(false)
const downloadLoading = ref(false)
const drawOptions = ref<any>({})
const loading = ref(false)
const userStore = useUserStore()
const posterRef = shallowRef<HTMLDivElement>()
const posterData = reactive({
    title: '',
    content: ''
})
const getData = async () => {
    loading.value = true
    try {
        const { data } = await getDecorate({ id: 12 })
        drawOptions.value = JSON.parse(data)[0]?.content || {}
    } finally {
        loading.value = false
    }
}

const inviteLink = computed(
    () => `${window.origin}/mobile?user_sn=${userStore.userInfo.sn}`
)
const { copy } = useCopy()
const pcLink = computed(
    () => `${window.origin}/?user_sn=${userStore.userInfo.sn}`
)

const download = async () => {
    try {
        downloadLoading.value = true
        await downloadHtml2Image(posterRef.value!)
    } catch (error) {
        feedback.msgError('下载失败，请重试')
    } finally {
        downloadLoading.value = false
    }
}
const open = (data) => {
    getData()
    showPopup.value = true
    posterData.title = data.title
    posterData.content = isString(data.content) ? [data.content] : data.content
}

defineExpose({
    open
})
</script>

<style lang="scss" scoped>
.poster {
    width: 100%;
    position: relative;
    overflow: hidden;
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
</style>
