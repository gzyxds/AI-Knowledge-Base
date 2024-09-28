<template>
    <div class="share-pop">
        <popup
            ref="popRef"
            width="auto"
            title="生成海报"
            :append-to-body="false"
            :click-modal-close="true"
            confirmButtonText=""
            cancelButtonText=""
        >
            <div class=" flex flex-col">
                <div class="flex-1 min-h-0">
                    <ElScrollbar>
                        <div ref="posterRef" class="w-[430px] bg-[#F8F8FB] rounded-[10px]">
                            <div class="flex justify-center bg-[#F2F3F6] rounded-t-[10px] overflow-hidden">
                                <img
                                    class="w-full object-contain"
                                    :src="imgContent.thumbnail || imgContent.image"
                                    alt=""
                                />
                            </div>
                            <div class="px-[16px] mt-[12px]">
                                <div
                                    class="title text-[16px] font-medium text-[#101010] line-clamp-2"
                                >
                                    {{
                                        imgContent?.prompts_cn ||
                                        imgContent.original_prompts.prompt
                                    }}
                                </div>
                            </div>
                            <div class="px-[15px]">
                                <el-divider border-style="solid" />
                            </div>
                            <div class="flex items-center px-[16px] pt-[0px] pb-[30px]">
                                <div>
                                    <div class="flex items-center">
                                        <img
                                            class="w-[45px] h-[45px] rounded-full"
                                            :src="userStore.userInfo.avatar"
                                        />
                                        <div class="ml-2">
                                            {{ userStore.userInfo.nickname }}
                                        </div>
                                    </div>
                                    <div
                                        class="mt-[16px] font-medium text-[#101010] text-xl"
                                    >
                                        {{ getWebsiteConfig.pc_title }}
                                    </div>
                                    <div class="mt-[10px] text-primary">
                                        {{ config.current_domain }}
                                    </div>
                                </div>
                                <div class="ml-auto h-[80px] p-[5px] bg-white rounded-[5px]">
                                    <QrcodeVue
                                        :value="inviteLink"
                                        :size="80"
                                        :margin="1"
                                    />
                                </div>
                            </div>
                        </div>
                    </ElScrollbar>
                </div>
            </div>
            <template #footer>
                <div class="flex px-[10px]">
                    <el-button
                        class="flex-1"
                        text
                        bg
                        plain
                        size="large"
                        @click="copy(pcLink)"
                    >
                        复制链接
                    </el-button>
                    <el-button
                        class="flex-1"
                        type="primary"
                        size="large"
                        @click="download"
                    >
                        下载海报
                    </el-button>
                </div>
            </template>
        </popup>
    </div>
</template>

<script setup lang="ts">
import QrcodeVue from 'qrcode.vue'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'

const popRef = shallowRef()
//海报ref
const posterRef = shallowRef()
const userStore = useUserStore()
const { getWebsiteConfig, config } = useAppStore()
const imgContent: any = ref({})

const open = (option: any) => {
    popRef.value.open()
    imgContent.value = option
}

const inviteLink = computed(
    () => `${config.current_domain}/mobile/packages/pages/robot_square/robot_square?type=2?user_sn=${userStore.userInfo.sn}`
)

const { copy } = useCopy()
const pcLink = computed(
    () => `${config.current_domain}/pc/robot_square?type=2&user_sn=${userStore.userInfo.sn}`
)

const downloadLoading = ref(false)
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

defineExpose({
    open
})
</script>

<style scoped lang="scss">
.share-pop {
    :deep() {
        .el-dialog__header {
            font-weight: 500;
            padding-bottom: 0;
        }
        .el-dialog {
            border-radius: 15px;
        }
        .el-dialog__body {
            padding: 15px 10px !important;
        }
    }
}
</style>
