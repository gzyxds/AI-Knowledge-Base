<template>
    <div class="redeem-code-query-pop" v-if="queryVisible">
        <ElDialog
            v-model="queryVisible"
            title="卡密兑换"
            width="600"
            @close="emit('close')"
        >
            <div class="flex justify-between pt-4 text-base" v-if="appStore.getRedeemCode?.is_show">
                <div>
                    <span class="mr-2">购买链接:</span>
                    <span>{{ appStore.getRedeemCode.buy_site }}</span>
                </div>
                <ElButton type="primary" :link="true" @click="copy(appStore.getRedeemCode.buy_site)">复制</ElButton>
            </div>

            <div class="flex pt-[40px]">
                <div class="flex-1">
                    <ElInput v-model="code" placeholder="请输入卡密编号" size="large"></ElInput>
                </div>
                <div class="flex justify-end w-[110px]">
                    <el-button
                        type="primary"
                        size="large"
                        :loading="isQuery"
                        @click="queryRedeem"
                    >
                        查询
                    </el-button>
                </div>
            </div>
        </ElDialog>
    </div>

    <div class="redeem-code-check-pop" v-if="checkVisible">
        <ElDialog v-model="checkVisible" width="400">
            <template #header>
                <div class="text-lg text-center font-medium">查询结果</div>
            </template>
            <div class="h-full">
                <el-form-item label="卡密类型：">
                    {{ checkResult.type_desc }}
                </el-form-item>
                <el-form-item label="卡密面额：">
                    {{ checkResult.content }}
                </el-form-item>
                <el-form-item label="兑换时间：">
                    {{ checkResult.failure_time }}
                </el-form-item>
                <el-form-item label="有效期至：" v-if="checkResult.valid_time">
                    {{ checkResult.valid_time }}
                </el-form-item>
            </div>
            <div class="flex-1 flex justify-center items-center bg-white pt-[20px]">
                <el-button
                    class="w-full"
                    type="primary"
                    size="large"
                    :loading="isUse"
                    @click="onUseRedeemCode"
                >
                    立即兑换
                </el-button>
            </div>
        </ElDialog>
    </div>
</template>
<script setup lang="ts">
import {copy} from '@/utils/util'
import {useAppStore} from '~/stores/app'
import {useUserStore} from '~/stores/user'
import {useLockFn} from '~/composables/useLockFn'
import type {RedeemCodeResponse} from '~/api/user'
import {checkRedeemCode, useRedeemCode} from '~/api/user'

const emit = defineEmits(['close'])
const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()
// 兑换码
const code = ref<string>('')
// 显示查询弹窗
const queryVisible = ref<boolean>(true)
// 显示查询结果
const checkVisible = ref<boolean>(false)
// 查询结果
const checkResult = ref<RedeemCodeResponse>({
    content: '',
    failure_time: '',
    id: '',
    sn: '',
    type: '',
    type_desc: '',
    valid_time: ''
})

// 查询卡密
const {isLock: isQuery, lockFn: queryRedeem} = useLockFn(async () => {
    try {
        const data = await checkRedeemCode({sn: code.value})
        checkVisible.value = true
        checkResult.value = data
    } catch (error) {
        code.value = ''
        console.log('查询卡密失败=>', error)
    }
})

// 兑换卡密
const {isLock: isUse, lockFn: onUseRedeemCode} = useLockFn(async () => {
    try {
        await useRedeemCode({sn: code.value})
        feedback.msgSuccess('兑换成功')
        checkVisible.value = false
        queryVisible.value = false
        code.value = ''
        await userStore.getUser()
        await router.push({
            path: '/user/record',
            query: {
                time: new Date().getTime()
            }
        })
        emit('close')
    } catch (error) {
        console.log('兑换卡密失败=>', error)
    }
})
</script>

<style lang="scss" scoped>
.redeem-code-btn {
    display: flex;
    align-items: center;
    padding: 5px 8px;
    background: #FFEEEE;
    border-radius: 5px;
    color: #FA5151;
    @apply cursor-pointer;
}

.redeem-code-query-pop {
    :deep() {
        .el-dialog {
            border-radius: 15px !important;
            background-image: url("@/assets/image/redeem_code_pop.png");
            background-size: 100% auto;
            background-repeat: no-repeat;
            padding-bottom: 24px;
        }

        .el-dialog__header {
            font-weight: 500;
            padding: 10px 10px 0 18px;
        }

        .el-dialog__headerbtn {
            right: 24px;
            padding-top: 20px;
            font-size: 24px;
            font-weight: bold;
        }
    }
}

.redeem-code-check-pop {
    :deep() {
        .el-dialog {
            border-radius: 15px !important;
        }
        .el-dialog__headerbtn {
            padding-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }
    }
}
</style>
