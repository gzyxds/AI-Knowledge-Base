<template>
    <div class="apply-pop">
        <ElDialog
            v-model="popShow"
            :width="`${typeList.length * 180}px`"
            title="提现"
            :close-on-click-modal="false"
            class="!rounded-[20px] min-w-[580px]"
            @close="closePop"
        >
            <el-form
                ref="ruleFormRef"
                :rules="rules"
                size="large"
                :model="formData"
                label-width="95px"
                v-loading="!typeList.length"
            >
                <el-form-item label="我的金额">
                    <div class="text-base text-[#FA5151] font-medium">
                        {{ canWithdrawBalance }}
                    </div>
                </el-form-item>
                <el-form-item label="提现金额" prop="money">
                    <div class="w-[280px]">
                        <el-input
                            placeholder="输入提现金额"
                            v-model="formData.money"
                        >
                            <template #append> 元</template>
                        </el-input>
                    </div>
                </el-form-item>
                <el-form-item label="提现方式">
                    <div class="flex">
                        <div
                            class="flex flex-col items-center w-[120px] pt-[12px] inactive rounded-lg mr-[20px] cursor-pointer"
                            :class="{
                            active: formData.type == item.id
                        }"
                            v-for="(item, index) in typeList"
                            :key="index"
                            @click="selectType(item.id)"
                        >
                            <img
                                class="w-[24px] h-[24px]"
                                :src="item.image"
                                alt=""
                            />
                            <div class="ml-2">{{ item.title }}</div>
                            <div v-if="formData.type == item.id" class="select-icon">
                                <Icon class="el-icon-select" name="el-icon-Select"/>
                            </div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item
                    :label="`${
                    formData.type == 3 ? '微信' : '支付宝'
                }账号`"
                    prop="account"
                    v-if="formData.type !== 2"
                >
                    <div class="w-[280px]">
                        <el-input
                            :placeholder="`请输入${
                                            formData.type == 3
                                                ? '微信'
                                                : '支付宝'
                                        }账号`"
                            v-model="formData.account"
                        >
                        </el-input>
                    </div>
                </el-form-item>
                <el-form-item
                    label="真实姓名"
                    prop="real_name"
                    v-if="formData.type !== 2"
                >
                    <div class="w-[280px]">
                        <el-input
                            placeholder="请输入真实姓名"
                            v-model="formData.real_name"
                        >
                        </el-input>
                    </div>
                </el-form-item>
                <el-form-item
                    label="收款二维码"
                    class="is-required"
                    v-if="formData.type == 3 || formData.type == 4"
                >
                    <CropperUpload @change="(value) => formData.money_qr_code = value">
                        <div
                            class="w-[100px] h-[100px]"
                            style="border: 1px dashed #e2e2e2"
                            v-if="!formData.money_qr_code"
                        >
                            <div
                                class="text-[#888888] flex flex-col items-center justify-center mt-[20px]"
                            >
                                <Icon
                                    size="30px"
                                    name="el-icon-Plus"
                                    color="#888888"
                                ></Icon>
                                <div>上传二维码</div>
                            </div>
                        </div>
                        <el-image
                            class="w-[100px] h-[100px]"
                            :src="formData.money_qr_code"
                            v-if="formData.money_qr_code"
                        ></el-image>
                    </CropperUpload>
                </el-form-item>
            </el-form>

            <div class="text-base text-[#9E9E9E]" v-if="withdrawDesc">提现说明：{{ withdrawDesc }}</div>

            <template #footer>
                <el-button @click="closePop">
                    取消
                </el-button>
                <el-button
                    type="primary"
                    @click="apply"
                >
                    确认提现
                </el-button>
            </template>
        </ElDialog>
    </div>
</template>

<script setup lang="ts">
import {applyWithdraw, withdrawWay, distributionCenter} from '@/api/promotion'
import type {FormInstance, FormRules} from 'element-plus'

const emit = defineEmits(['closePop'])

//弹框显示/隐藏
const popShow = ref<boolean>(false)
//表单ref
const ruleFormRef = ref<FormInstance>()

const formData = reactive<any>({
    money_qr_code: '',
    money: '',
    account: '',
    real_name: '',
    type: 3 //提现方式：1-支付宝；2-微信零钱；3-微信收款码；4-支付宝收款码；
})

//提现方式
const typeList: any = ref([])

//缓存的支付信息
const cacheAli = ref<{
    account: string
    real_name: string
    [key: string]: string
}>({
    account: '',
    real_name: ''
})
const cacheWx = ref<{
    account: string
    real_name: string
    [key: string]: string
}>({
    account: '',
    real_name: ''
})
// 可提现余额
const canWithdrawBalance = ref(0)
// 提现说明
const withdrawDesc = ref('')

//表单校验规则
const rules = reactive<FormRules>({
    money: [{required: true, message: '请输入提现金额', trigger: 'blur'}],
    account: [{required: true, message: '请输入账号', trigger: 'blur'}],
    real_name: [{required: true, message: '请输入真实姓名', trigger: 'blur'}]
})

//分销数据
// const centerData: any = ref()
const getCenterData = async () => {
    const {withdraw_config, user} = await distributionCenter()

    withdrawDesc.value = withdraw_config.open ? withdraw_config.explain : ''
    canWithdrawBalance.value = user.user_money

    cacheAli.value.account = withdraw_config?.ali_acccount
    cacheAli.value.real_name = withdraw_config?.ali_name
    cacheWx.value.account = withdraw_config?.wechat_acccount
    cacheWx.value.real_name = withdraw_config?.wechat_name
}

//获取提现方式
const getWithdrawWayList = async () => {
    typeList.value = await withdrawWay()
    await selectType(typeList.value[0].id)
}

//选择提现方式
const selectType = async (value: any) => {
    formData.type = value
    await nextTick()
    //设置缓存数据
    if (value == 1 || value == 4) {
        Object.keys(cacheAli.value).map((item) => {
            formData[item] = cacheAli.value[item]
        })
    }
    if (value == 3) {
        Object.keys(cacheWx.value).map((item) => {
            formData[item] = cacheWx.value[item]
        })
    }
}

const apply = async () => {
    if (!ruleFormRef.value) {
        return
    }
    await ruleFormRef.value.validate()
    await feedback.confirm('请确认是否提现！')
    await applyWithdraw(formData)
    feedback.msgSuccess('申请成功！')
    ruleFormRef?.value.resetFields()
    closePop()
}

//打开弹框
const open = async () => {
    popShow.value = true
}

//关闭弹框
const closePop = () => {
    popShow.value = false
    emit('closePop')
}

onMounted(async () => {
    await getCenterData()
    await getWithdrawWayList()
})

defineExpose({open})
</script>

<style scoped lang="scss">
.apply-pop {
    :deep() {
        .el-dialog__header {
            padding-bottom: 0 !important;
        }
    }

    .active {
        border: 1px solid var(--el-color-primary) !important;
    }

    .inactive {
        position: relative;
        overflow: hidden;
        border: 1px solid;
        @apply border-br-light;
    }

    .select-icon {
        $size: 26px;
        position: absolute;
        right: -1px;
        bottom: -1px;
        width: $size;
        height: $size;
        display: flex;
        align-items: center;
        justify-content: center;
        clip-path: polygon(0 100%, 100% 0, 100% 100%);

        :deep() .el-icon-select {
            transform-origin: center center;
            transform: translate(35%, 35%);
        }

        @apply text-white bg-primary;
    }
}
</style>
