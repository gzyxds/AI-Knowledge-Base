<template>
    <div class="pb-[10px]">
        <div class="text-xl font-medium pt-4 pl-4">绑定微信</div>
        <div class="flex flex-col items-center mt-[20px] pt-3 pb-[30px]">
            <div class="relative w-[180px] h-[180px]" v-loading="pending">
                <ElImage
                    v-if="data.url"
                    :src="data.url"
                    class="w-full h-full"
                />
                <div
                    v-if="status == WxBindStatus.SCANNED_CODE"
                    class="absolute left-0 top-0 w-full h-full bg-mask"
                    style="background: rgba(0, 0, 0, 0.5)"
                >
                    <div
                        class="h-full text-primary flex justify-center items-center"
                    >
                        <Icon name="el-icon-SuccessFilled" :size="30" />
                    </div>
                </div>
                <div
                    v-if="
                        status == WxBindStatus.INVALID ||
                        status == WxBindStatus.BIND_FAIL ||
                        status == WxBindStatus.CODE_ERROR
                    "
                    class="absolute left-0 top-0 w-full h-full bg-overlay cursor-pointer"
                    style="background: rgba(0, 0, 0, 0.5)"
                    @click="refresh"
                >
                    <div
                        class="h-full flex flex-col justify-center items-center text-white"
                    >
                        <div>点击刷新</div>
                    </div>
                </div>
            </div>

            <template v-if="status == WxBindStatus.SCANNED_CODE">
                <div class="mt-3">扫码成功</div>
                <div class="mt-5 text-error text-sm">
                    请在微信公众号中确认绑定
                </div>
            </template>
            <template v-if="status == WxBindStatus.INVALID">
                <div class="mt-3 text-error">二维码失效</div>
                <div class="mt-5 text-sm">请在点击二维码刷新</div>
            </template>
            <template v-if="status == WxBindStatus.BIND_FAIL">
                <div class="mt-3 text-error">绑定失败，请重新绑定</div>
                <div class="mt-5 text-sm">请在点击二维码刷新</div>
            </template>
            <template
                v-if="
                    status == WxBindStatus.NORMAL ||
                    status == WxBindStatus.BIND_SUCCESS ||
                    status == WxBindStatus.CODE_ERROR
                "
            >
                <div class="mt-3">使用手机微信扫码绑定</div>
                <div class="mt-2 text-tx-placeholder">绑定后可使用微信扫码登录</div>
            </template>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { ElImage } from 'element-plus'
import { WxBindStatus, useCheckTicket, useGetCode } from './bindWeixin'

const { state: code, getData: getCode } = useGetCode()
const { pending, data } = toRefs(code.value)
const { start, end, status } = useCheckTicket(data)

const refresh = async () => {
    status.value = WxBindStatus.NORMAL
    try {
        await getCode()
        start()
    } catch (error) {
        status.value = WxBindStatus.CODE_ERROR
    }
}

onMounted(() => {
    refresh()
})
onUnmounted(() => {
    end()
})
</script>

<style lang="scss" scoped></style>
