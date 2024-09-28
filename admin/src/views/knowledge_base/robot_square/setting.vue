<template>
    <div class="draw-square-setting">
        <el-card shadow="never" class="!border-none mt-4">
            <div class="font-medium mb-7">智能体广场设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="显示用户信息">
                    <div>
                        <el-switch
                            v-model="formData.is_show_user"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">
                            开启后，前台智能体广场图片显示分享用户的信息，默认开启
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="审核设置">
                    <div>
                        <el-radio-group
                            v-model="formData.auto_audit"
                            :active-value="1"
                            :inactive-value="0"
                        >
                            <el-radio :label="1">自动通过审核</el-radio>
                            <el-radio :label="0">人工审核</el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            {{
                                formData.auto_audit ?
                                    '用户公开智能体，无需后台人工审核，系统自动通过' :
                                    '用户公开智能体，需要后台人工审核，审核通过后才显示在前台广场'
                            }}
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <footer-btns v-perms="['kb.square/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="robotSquareSetting">
import { getRobotSquareConfig, setRobotSquareConfig } from '@/api/knowledge_base/robot_square'

const formData = reactive<{
    is_show_user: number
    auto_audit: number
}>({
    is_show_user: 0,
    auto_audit: 0
})
const getData = async () => {
    const data = await getRobotSquareConfig()
    Object.keys(formData).map((item) => {
        //@ts-ignore
        formData[item] = data[item]
    })
}
const handleSubmit = async () => {
    await setRobotSquareConfig(formData)
    getData()
}
getData()
</script>

<style lang="scss" scoped></style>
