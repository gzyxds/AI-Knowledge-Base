<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <div class="text-xl font-medium mb-[20px]">邀请奖励</div>
            <el-form ref="ruleFormRef" :rules="rules" :model="pagerData" label-width="120px">
                <el-form-item label="是否开启" prop="is_open">
                    <el-switch v-model="pagerData.is_open" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="邀请一个人奖励" prop="one_award">
                    <div>
                        <div class="flex">
                            <div>
                                <el-input placeholder="请输入" v-model="pagerData.one_award"></el-input>
                            </div>
                            <div class="ml-[10px]">电力值</div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="每天最多邀请" prop="day_num">
                    <div class="flex">
                        <div>
                            <el-input placeholder="请输入" v-model="pagerData.day_num"></el-input>
                        </div>
                        <div class="ml-[10px]">个人有奖励</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <footer-btns v-perms="['market.activityReward/setInviteSetting']">
            <el-button type="primary" @click="handleSubmit(ruleFormRef)">保存</el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import { getInviteConfig, editInviteConfig } from '@/api/marketing/invite'
import feedback from '@/utils/feedback'
interface pagerDataInter {
    is_open: number
    one_award: number
    day_num: number
}
//表单ref
const ruleFormRef = ref<FormInstance>()
const pagerData = ref<pagerDataInter>({
    is_open: 1,
    one_award: 1,
    day_num: 5
})

//表单校验规则
const rules = reactive<FormRules>({
    day_num: [{ required: true, message: '请输入每天最多邀请几次数有奖励', trigger: 'blur' }]
})

/**
 * 初始化数据
 */
const getData = async () => {
    pagerData.value = await getInviteConfig()
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
            await editInviteConfig(pagerData.value)
            await getData()
        } else {
            feedback.msgError('电力值必须大于0')
        }
    } catch (error) {
        console.log(error)
    }
}
</script>
