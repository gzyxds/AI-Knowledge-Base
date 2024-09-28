<template>
    <div class="ai-square-setting">
        <el-card shadow="never" class="!border-none">
            <div class="font-medium mb-7">绘画广场设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="显示用户信息">
                    <div>
                        <el-switch
                            v-model="formData.draw_award.is_show_user"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">
                            开启后，前台绘画广场图片显示分享用户的信息，默认开启
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="审核设置">
                    <div>
                        <el-radio-group
                            v-model="formData.draw_award.auto_audit"
                            :active-value="1"
                            :inactive-value="0"
                        >
                            <el-radio :label="1">自动通过审核</el-radio>
                            <el-radio :label="0">人工审核</el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            {{
                                formData.draw_award.auto_audit ?
                                    '用户分享绘画作品，无需后台人工审核，系统自动通过' :
                                    '用户分享绘画作品，需要后台人工审核，审核通过后才显示在前台广场'
                            }}
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card shadow="never" class="!border-none mt-4">
            <div class="font-medium mb-7">音乐广场设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="显示用户信息">
                    <div>
                        <el-switch
                            v-model="formData.music_award.is_show_user"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">
                            开启后，前台音乐广场图片显示分享用户的信息，默认开启
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="审核设置">
                    <div>
                        <el-radio-group
                            v-model="formData.music_award.auto_audit"
                            :active-value="1"
                            :inactive-value="0"
                        >
                            <el-radio :label="1">自动通过审核</el-radio>
                            <el-radio :label="0">人工审核</el-radio>
                        </el-radio-group>
                        <div class="form-tips">

                            {{
                                formData.music_award.auto_audit ?
                                    '用户分享音乐作品，无需后台人工审核，系统自动通过' :
                                    '用户分享音乐作品，需要后台人工审核，审核通过后才显示在前台广场'
                            }}
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card shadow="never" class="!border-none mt-4">
            <div class="font-medium mb-7">视频广场设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="显示用户信息">
                    <div>
                        <el-switch
                            v-model="formData.video_award.is_show_user"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">
                            开启后，前台视频广场图片显示分享用户的信息，默认开启
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="审核设置">
                    <div>
                        <el-radio-group
                            v-model="formData.video_award.auto_audit"
                            :active-value="1"
                            :inactive-value="0"
                        >
                            <el-radio :label="1">自动通过审核</el-radio>
                            <el-radio :label="0">人工审核</el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            {{
                                formData.video_award.auto_audit ?
                                    '用户分享视频作品，无需后台人工审核，系统自动通过' :
                                    '用户分享视频作品，需要后台人工审核，审核通过后才显示在前台广场'
                            }}
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <footer-btns v-perms="['market.activityReward/setSquareSetting']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="AiSquareSetting">
import type {AiSquareSetFormType} from '@/api/ai_application/ai_square'
import {aiSquareGetConfig, aiSquareSetConfig} from '@/api/ai_application/ai_square'

const formData = reactive<AiSquareSetFormType>({
    draw_award: {
        is_show_user: 0, // 是  显示用户信息：1-开启；0-关闭；
        auto_audit: 0 // 是  自动通过审核：1-开启；0-关闭；
    },
    music_award: {
        is_show_user: 0, // 是  显示用户信息：1-开启；0-关闭；
        auto_audit: 0 // 是  自动通过审核：1-开启；0-关闭；
    },
    video_award: {
        is_show_user: 0, // 是  显示用户信息：1-开启；0-关闭；
        auto_audit: 0 // 是  自动通过审核：1-开启；0-关闭；
    }
})
const getData = async () => {
    const data = await aiSquareGetConfig()
    Object.keys(formData).map((item) => {
        //@ts-ignore
        formData[item] = data[item]
    })
}
const handleSubmit = async () => {
    await aiSquareSetConfig(formData)
    getData()
}
getData()
</script>

<style lang="scss" scoped></style>
