<template>
    <div>
        <el-form ref="ruleFormRef" :rules="rules" :model="pagerData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">绘画分享奖励</div>
                <el-form-item label="是否开启" prop="is_open">
                    <el-switch v-model="pagerData.draw_award.is_open" :active-value="1" :inactive-value="0"/>
                </el-form-item>
                <el-form-item label="分享一次奖励" prop="one_award">
                    <div>
                        <div class="flex">
                            <div>
                                <el-input placeholder="请输入" v-model="pagerData.draw_award.one_award"></el-input>
                            </div>
                            <div class="ml-[10px]">电力值</div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="每天最多分享" prop="day_num">
                    <div class="flex">
                        <div>
                            <el-input placeholder="请输入" v-model="pagerData.draw_award.day_num"></el-input>
                        </div>
                        <div class="ml-[10px]">次有奖励</div>
                    </div>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">音乐分享奖励</div>
                <el-form-item label="是否开启" prop="is_open">
                    <el-switch v-model="pagerData.music_award.is_open" :active-value="1" :inactive-value="0"/>
                </el-form-item>
                <el-form-item label="分享一次奖励" prop="one_award">
                    <div>
                        <div class="flex">
                            <div>
                                <el-input placeholder="请输入" v-model="pagerData.music_award.one_award"></el-input>
                            </div>
                            <div class="ml-[10px]">电力值</div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="每天最多分享" prop="day_num">
                    <div class="flex">
                        <div>
                            <el-input placeholder="请输入" v-model="pagerData.music_award.day_num"></el-input>
                        </div>
                        <div class="ml-[10px]">次有奖励</div>
                    </div>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">视频分享奖励</div>
                <el-form-item label="是否开启" prop="is_open">
                    <el-switch v-model="pagerData.video_award.is_open" :active-value="1" :inactive-value="0"/>
                </el-form-item>
                <el-form-item label="分享一次奖励" prop="one_award">
                    <div>
                        <div class="flex">
                            <div>
                                <el-input placeholder="请输入" v-model="pagerData.video_award.one_award"></el-input>
                            </div>
                            <div class="ml-[10px]">电力值</div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="每天最多分享" prop="day_num">
                    <div class="flex">
                        <div>
                            <el-input placeholder="请输入" v-model="pagerData.video_award.day_num"></el-input>
                        </div>
                        <div class="ml-[10px]">次有奖励</div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['market.activityReward/setWorksSetting']">
            <el-button type="primary" @click="handleSubmit(ruleFormRef)">保存</el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import type {FormInstance, FormRules} from 'element-plus'
import {getWorksConfig, editWorksConfig} from '@/api/marketing/works'
import feedback from '@/utils/feedback'

interface pagerDataInter {
    draw_award: {
        is_open: 0 | 1
        one_award: string
        day_num: string
        auto_audit: 0 | 1
    }
    music_award: {
        is_open: 0 | 1
        one_award: string
        day_num: string
        auto_audit: 0 | 1
    }
    video_award: {
        is_open: 0 | 1
        one_award: string
        day_num: string
        auto_audit: 0 | 1
    }
}

//表单ref
const ruleFormRef = ref<FormInstance>()
const pagerData = ref<pagerDataInter>({
    draw_award: {
        is_open: 0,
        one_award: '',
        day_num: '',
        auto_audit: 0
    },
    music_award: {
        is_open: 0,
        one_award: '',
        day_num: '',
        auto_audit: 0
    },
    video_award: {
        is_open: 0,
        one_award: '',
        day_num: '',
        auto_audit: 0
    }
})

//表单校验规则
const rules = reactive<FormRules>({
    // day_num: [{required: true, message: '请输入每天最多分享几次数有奖励', trigger: 'blur'}]
})

/**
 * 初始化数据
 */
const getData = async () => {
    pagerData.value = await getWorksConfig()
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
        await editWorksConfig(pagerData.value)
        await getData()
    } catch (error) {
        console.log(error)
    }
}
</script>
