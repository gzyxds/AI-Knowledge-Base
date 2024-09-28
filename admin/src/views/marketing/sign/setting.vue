<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <div class="text-xl font-medium mb-[20px]">签到奖励</div>
            <el-form ref="ruleFormRef" :rules="rules" :model="pagerData" label-width="120px">
                <el-form-item label="是否开启">
                    <el-switch v-model="pagerData.is_open" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="签到一次奖励" prop="rewards">
                    <div>
                        <div class="flex">
                            <div>
                                <el-input placeholder="请输入" v-model="pagerData.one_award"></el-input>
                            </div>
                            <div class="ml-[10px]">电力值</div>
                        </div>
                    </div>
                </el-form-item>
                <!--            <el-form-item label="每天最多签到" prop="max_share">-->
                <!--                <div class="flex">-->
                <!--                    <div>-->
                <!--                        <el-input placeholder="请输入" v-model="pagerData.max_share"></el-input>-->
                <!--                    </div>-->
                <!--                    <div class="ml-[10px]">次有奖励</div>-->
                <!--                </div>-->
                <!--            </el-form-item>-->
            </el-form>
        </el-card>
        <footer-btns>
            <el-button
                v-perms="['market.activityReward/setSignSetting']"
                type="primary"
                @click="handleSubmit(ruleFormRef)"
            >
                保存
            </el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import { getSignConfig, editSignConfig } from '@/api/marketing/sign'
import feedback from '@/utils/feedback'

interface pagerDataInter {
    is_open: number
    one_award: number
}

//表单ref
const ruleFormRef = ref<FormInstance>()
const pagerData = ref<pagerDataInter>({
    is_open: 1,
    one_award: 1
})

//表单校验规则
const rules = reactive<FormRules>({
    // max_share: [{ required: true, message: '请输入每天最多签到几次数有奖励', trigger: 'blur' }]
})

/**
 * 初始化数据
 */
const getData = async () => {
    pagerData.value = await getSignConfig()
}
getData()
/**
 * 提交数据
 */
const handleSubmit = async (formEl: FormInstance | undefined) => {
    if (!formEl) {
        console.log(formEl)
        return
    }
    try {
        await formEl.validate()
        if (pagerData.value.one_award > 0) {
            await editSignConfig(pagerData.value)
            await getData()
        } else {
            feedback.msgError('电力值必须大于0')
        }
    } catch (error) {
        console.log(error)
    }
}
</script>
