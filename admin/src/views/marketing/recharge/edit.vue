<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="title" @back="$router.back()" />
        </el-card>
        <el-form
            class="ls-form mt-4"
            ref="formRef"
            :rules="rules"
            :model="formData"
            label-width="120px"
            v-loading="loading"
        >
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">套餐信息</div>

                <el-form-item label="套餐名称" prop="name">
                    <div class="w-[380px]">
                        <el-input placeholder="请输入套餐名称" v-model="formData.name" />
                    </div>
                </el-form-item>
                <!-- <el-form-item label="套餐描述" prop="remarks">
                    <div class="w-[380px]">
                        <el-input
                            placeholder="请输入套餐介绍信息，用于对外展示"
                            type="textarea"
                            :rows="4"
                            v-model="formData.remarks"
                        />
                    </div>
                </el-form-item> -->
                <el-form-item label="套餐价格" prop="sell_price">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.sell_price"
                            type="number"
                            clearable
                            placeholder="请输入实际售价"
                        >
                            <template #append>元</template>
                        </el-input>
                    </div>
                </el-form-item>
                <!-- 划线价 -->
                <el-form-item label="原价">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.line_price"
                            clearable
                            placeholder="请输入原价"
                        />
                        <div class="form-tips">不填写就不显示</div>
                    </div>
                </el-form-item>
                <el-form-item label="套餐标签">
                    <div class="w-[380px]">
                        <el-input v-model="formData.tags" clearable placeholder="请输入套餐标签" />
                    </div>
                </el-form-item>
                <el-form-item label="排序">
                    <div class="w-[380px]">
                        <el-input v-model="formData.sort" clearable placeholder="请输入排序" />
                        <div class="form-tips">默认为0，排序值越大越排前面</div>
                    </div>
                </el-form-item>
                <el-form-item label="状态" prop="status" required>
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">套餐内容</div>

                <el-form-item label="电力值数量" prop="chat_balance">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.chat_balance"
                            type="number"
                            clearable
                            placeholder="不填写默认为0"
                        >
                            <template #append>电力值</template>
                        </el-input>
                    </div>
                </el-form-item>
                <el-form-item label="智能体数量" prop="robot_number">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.robot_number"
                            type="number"
                            clearable
                            placeholder="不填写默认为0"
                        >
                            <template #append>个</template>
                        </el-input>
                    </div>
                </el-form-item>
                <!-- <el-form-item label="知识库数量" prop="kb_number">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.kb_number"
                            type="number"
                            clearable
                            placeholder="不填写默认为0"
                        >
                            <template #append>个</template>
                        </el-input>
                    </div>
                </el-form-item> -->
                <!-- <el-form-item label="视频合成时长" prop="video_duration">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.video_duration"
                            type="number"
                            clearable
                            placeholder="不填写默认为0"
                        >
                            <template #append>分钟</template>
                        </el-input>
                    </div>
                </el-form-item> -->
                <el-form-item label="开启赠送" prop="is_give">
                    <el-switch v-model="formData.is_give" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <template v-if="formData.is_give == 1">
                    <el-form-item label="电力值数量" prop="give_chat_balance">
                        <div class="w-[380px]">
                            <el-input
                                v-model="formData.give_chat_balance"
                                type="number"
                                clearable
                                placeholder="不填写默认为0"
                            >
                                <template #append>电力值</template>
                            </el-input>
                        </div>
                    </el-form-item>
                    <el-form-item label="智能体数量" prop="give_robot_number">
                        <div class="w-[380px]">
                            <el-input
                                v-model="formData.give_robot_number"
                                type="number"
                                clearable
                                placeholder="不填写默认为0"
                            >
                                <template #append>个</template>
                            </el-input>
                        </div>
                    </el-form-item>
                    <!-- <el-form-item label="知识库数量" prop="give_kb_number">
                        <div class="w-[380px]">
                            <el-input
                                v-model="formData.give_kb_number"
                                type="number"
                                clearable
                                placeholder="不填写默认为0"
                            >
                                <template #append>个</template>
                            </el-input>
                        </div>
                    </el-form-item> -->
                    <!-- <el-form-item label="视频合成时长" prop="give_video_duration">
                        <div class="w-[380px]">
                            <el-input
                                v-model="formData.give_video_duration"
                                type="number"
                                clearable
                                placeholder="不填写默认为0"
                            >
                                <template #append>分钟</template>
                            </el-input>
                        </div>
                    </el-form-item> -->
                </template>
            </el-card>
        </el-form>
        <footer-btns>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import { rechargeEdit, rechargeAdd, getRechargeDetail } from '@/api/marketing/recharge'
import feedback from "@/utils/feedback";
const formRef = shallowRef<FormInstance>()
const { query } = useRoute()
const router = useRouter()
const title = computed(() => {
    return query.mode == 'edit' ? '编辑充值套餐' : '新增充值套餐'
})

//表单数据
const formData = reactive<any>({
    id: '',
    name: '',
    remarks: '',
    sell_price: '',
    line_price: '',
    status: 1,
    chat_balance: '',
    robot_number: '',
    kb_number: '',
    is_give: 0,
    give_chat_balance: '',
    give_robot_number: '',
    give_kb_number: '',
    video_duration: '',
    give_video_duration: '',
    sort: 0,
    tags: '' //套餐标签
})

//表单校验规则
const rules = {
    name: [
        {
            required: true,
            message: '请输入套餐名称'
        }
    ],
    sell_price: [
        {
            required: true,
            message: '请输入套餐价格'
        }
    ]
}

//提交
const handleSave = async () => {
    await formRef.value?.validate()
    if (formData.sell_price < 0) {
        feedback.msgError('套餐价格不能少于0')
        return
    }
    if (formData.line_price < 0) {
        feedback.msgError('原价不能少于0')
        return
    }
    if (formData.chat_balance < 0) {
        feedback.msgError('电力值数量不能少于0')
        return
    }

    query.mode == 'edit' ? await rechargeEdit(formData) : await rechargeAdd(formData)
    router.back()
}
const loading = ref(false)
const getDetail = async () => {
    if (!query.id) return
    loading.value = true
    const data = await getRechargeDetail({
        id: query.id
    })
    Object.keys(data).forEach((key) => {
        //@ts-ignore
        formData[key] = data[key]
    })
    loading.value = false
}

getDetail()
</script>
