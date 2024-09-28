<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header content="形象详情" @back="$router.back()" />
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-form
                class="p-4"
                ref="formRef"
                :model="formData"
                label-width="140px"
                :rules="formRules"
                :disabled="true"
            >
                <el-form-item label="形象名称" prop="name">
                    <div class="w-[420px]">
                        <el-input v-model="formData.name" placeholder="请输入形象名称" clearable />
                    </div>
                </el-form-item>
                <el-form-item label="形象头像" prop="avatar">
                    <div>
                        <div>
                            <MaterialPicker :disabled="true" v-model="formData.avatar" />
                        </div>
                        <div class="form-tips">建议尺寸：50*50px</div>
                    </div>
                </el-form-item>
                <el-form-item label="形象封面" prop="image">
                    <div>
                        <div>
                            <MaterialPicker :disabled="true" v-model="formData.image" />
                        </div>
                        <div class="form-tips">建议尺寸：280px×187px</div>
                    </div>
                </el-form-item>
                <el-form-item label="宽屏人物待机视频" prop="wide_stay_video">
                    <div>
                        <div>
                            <MaterialPicker
                                :disabled="true"
                                type="video"
                                v-model="formData.wide_stay_video"
                            />
                        </div>
                        <div class="form-tips">
                            格式为MP4，大小不能超过20M
                            <el-popover placement="top-start" width="auto" trigger="hover">
                                <template #reference>
                                    <span class="text-primary"> 示例 </span>
                                </template>

                                <img
                                    src="@/assets/images/digital_example1.png"
                                    alt=""
                                    class="w-[270px] h-[170px]"
                                />
                            </el-popover>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="宽屏人物说话视频" prop="wide_talk_video">
                    <div>
                        <div>
                            <MaterialPicker
                                :disabled="true"
                                type="video"
                                v-model="formData.wide_talk_video"
                            />
                        </div>
                        <div class="form-tips">
                            格式为MP4，大小不能超过20M
                            <el-popover placement="top-start" width="auto" trigger="hover">
                                <template #reference>
                                    <span class="text-primary"> 示例 </span>
                                </template>

                                <img
                                    src="@/assets/images/digital_example1.png"
                                    alt=""
                                    class="w-[270px] h-[170px]"
                                />
                            </el-popover>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="竖屏人物待机视频" prop="vertical_stay_video">
                    <div>
                        <div>
                            <MaterialPicker
                                :disabled="true"
                                type="video"
                                v-model="formData.vertical_stay_video"
                            />
                        </div>
                        <div class="form-tips">
                            格式为MP4，大小不能超过20M
                            <el-popover placement="top-start" width="auto" trigger="hover">
                                <template #reference>
                                    <span class="text-primary"> 示例 </span>
                                </template>

                                <img
                                    src="@/assets/images/digital_example2.png"
                                    alt=""
                                    class="w-[270px] h-[350px]"
                                />
                            </el-popover>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="竖屏人物说话视频" prop="vertical_talk_video">
                    <div>
                        <div>
                            <MaterialPicker
                                :disabled="true"
                                type="video"
                                v-model="formData.vertical_talk_video"
                            />
                        </div>
                        <div class="form-tips">
                            格式为MP4，大小不能超过20M
                            <el-popover placement="top-start" width="auto" trigger="hover">
                                <template #reference>
                                    <span class="text-primary"> 示例 </span>
                                </template>

                                <img
                                    src="@/assets/images/digital_example2.png"
                                    alt=""
                                    class="w-[270px] h-[350px]"
                                />
                            </el-popover>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="配音角色" prop="dubbing">
                    <div class="flex flex-1">
                        {{ formData.dubbing_name }}
                    </div>
                </el-form-item>
                <el-form-item label="自定义闲时时间" prop="idle_time">
                    <div class="w-[420px]">
                        <el-input
                            v-model="formData.idle_time"
                            placeholder="请输入自定义闲时时间"
                            type="number"
                        >
                            <template #append>秒</template>
                        </el-input>
                        <div class="form-tips">
                            例如：选择5s，每隔5秒就会有一个回复内容，内容是在闲时回复内容的文案
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="闲时回复内容" prop="idle_reply">
                    <div class="w-[420px]">
                        <el-input
                            v-model="formData.idle_reply"
                            placeholder="请输入闲时回复内容"
                            type="textarea"
                            :rows="4"
                            clearable
                        />
                        <div class="form-tips">根据自定义闲时时间段设置后形象回复的内容</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import { getDigitalDetail } from '@/api/knowledge_base/digital'
const route = useRoute()
const id = computed(() => route.query.id)

const formData = ref({
    name: '',
    avatar: '',
    image: '',
    wide_stay_video: '',
    wide_talk_video: '',
    vertical_stay_video: '',
    vertical_talk_video: '',
    channel: '',
    dubbing_name: '',
    idle_time: 10,
    idle_reply: ''
})

const idleTimeOptions = ref([
    {
        value: 5,
        label: '5秒'
    },
    {
        value: 10,
        label: '10秒'
    },
    {
        value: 15,
        label: '15秒'
    },
    {
        value: 20,
        label: '20秒'
    },
    {
        value: 25,
        label: '25秒'
    },
    {
        value: 30,
        label: '30秒'
    }
])
const formRef = shallowRef<FormInstance>()
const formRules = shallowReactive<FormRules>({
    name: [
        {
            required: true,
            message: '请输入形象名称'
        }
    ],
    avatar: [
        {
            required: true,
            type: 'string',
            message: '请选择形象头像'
        }
    ],
    image: [
        {
            required: true,
            type: 'string',
            message: '请选择形象封面'
        }
    ],
    wide_stay_video: [
        {
            required: true,
            type: 'string',
            message: '请选择宽屏人物待机视频'
        }
    ],
    wide_talk_video: [
        {
            required: true,
            type: 'string',
            message: '请选择宽屏人物说话视频'
        }
    ],
    vertical_stay_video: [
        {
            required: true,
            type: 'string',
            message: '请选择竖屏人物待机视频'
        }
    ],
    vertical_talk_video: [
        {
            required: true,
            type: 'string',
            message: '请选择竖屏人物说话视频'
        }
    ],
    dubbing: [
        {
            required: true,
            message: '请选择配音角色'
        }
    ],
    idle_time: [
        {
            required: true,
            message: '请选择自定义闲时时间'
        }
    ],
    idle_reply: [
        {
            required: true,
            type: 'string',
            message: '请输入闲时回复内容'
        }
    ]
})

const getData = async () => {
    if (id.value) {
        formData.value = await getDigitalDetail({ id: id.value })
    }
}

getData()
</script>
