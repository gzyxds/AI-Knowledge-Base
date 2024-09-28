<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="100px"
            >
                <el-form-item label="封面" prop="image_url">
                    <materialPicker
                        v-model="formData.image_url"
                        :disabled="isSourceFromUser"
                    ></materialPicker>
                </el-form-item>
                <!--                <el-form-item :label="isSourceFromUser ? '用户输入' : '中文提示词'">-->
                <!--                    <el-input-->
                <!--                        :disabled="isSourceFromUser"-->
                <!--                        class="w-[380px]"-->
                <!--                        v-model="formData.prompts_cn"-->
                <!--                        type="textarea"-->
                <!--                        :autosize="{ minRows: 4, maxRows: 20 }"-->
                <!--                        :placeholder="isSourceFromUser ? '' : '请输入中文提示词'"-->
                <!--                    />-->
                <!--                </el-form-item>-->
                <el-form-item label="歌曲名称">
                    <el-input
                        class="w-[380px]"
                        v-model="formData.title"
                        placeholder="请输入用户昵称"
                        :disabled="isSourceFromUser"
                    />
                </el-form-item>
                <el-form-item label="歌词" prop="lyric">
                    <el-input
                        :disabled="isSourceFromUser"
                        class="w-[380px]"
                        v-model="formData.lyric"
                        type="textarea"
                        :autosize="{ minRows: 4, maxRows: 20 }"
                        placeholder="请输入英文提示词"
                    />
                </el-form-item>
                <el-form-item label="音频" prop="audio_url">
                    <materialPicker
                        v-model="formData.audio_url"
                        type="audio"
                        :disabled="isSourceFromUser"
                    ></materialPicker>
                </el-form-item>
                <el-form-item label="音频时长">
                    {{ formData.duration }}
                </el-form-item>

                <el-form-item label="所属分类" prop="category_id">
                    <div>
                        <el-select
                            class="w-[380px]"
                            v-model="formData.category_id"
                            placeholder="全部"
                        >
                            <el-option
                                v-for="item in catePager.lists"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                        <div class="form-tips">如不选择分类，则默认显示在前台的【全部】中</div>
                    </div>
                </el-form-item>
                <el-form-item v-if="mode != 'edit' || formData.source == 1" label="用户头像" prop="avatar">
                    <materialPicker
                        v-model="formData.avatar"
                    ></materialPicker>
                </el-form-item>
                <el-form-item v-if="mode != 'edit' || formData.source == 1" label="用户昵称">
                    <el-input
                        :disabled="isSourceFromUser"
                        class="w-[380px]"
                        v-model="formData.nickname"
                        placeholder="请输入用户昵称"
                    />
                </el-form-item>
                <el-form-item label="是否显示" prop="is_show">
                    <el-switch v-model="formData.is_show" :active-value="1" :inactive-value="0"/>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type {FormInstance} from 'element-plus'
import {
    addMusicSquare,
    editMusicSquare,
    getMusicSquareDetail,
    getSquareCategoryAll
} from '@/api/ai_application/ai_square'
import Popup from '@/components/popup/index.vue'

const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
//弹框标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑音乐' : '新增音乐'
})
//表单数据
const formData = reactive({
    id: '',
    title: '',
    lyric: '',
    audio_url: '',
    sort: 0,
    is_show: 1,
    category_id: '',
    avatar: '',
    nickname: '',
    image_url: '',
    duration: '',
    source: 1
})
const catePager = reactive({
    loading: true,
    lists: []
})
//校验规则
const rules = {
    image_url: [
        {
            required: true,
            message: '请选择图片',
            trigger: ['blur']
        }
    ]
}

const isSourceFromUser = computed(() => {
    return formData.source == 2
})
const getData = async () => {
    catePager.loading = true
    try {
        const lists = await getSquareCategoryAll({
            type: 2
        })
        catePager.lists = lists
        catePager.loading = false
    } catch (error) {
        catePager.loading = false
        console.log('获取绘画分类失败=>', error)
    }
}

//提交
const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit' ? await editMusicSquare(formData) : await addMusicSquare(formData)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = (type = 'add', id?: number) => {
    mode.value = type
    popupRef.value?.open()
    getData()
    if (id) getDetails(id)
}

const getDetails = async (id?: number) => {
    try {
        const data = await getMusicSquareDetail({id})
        setFormData(data)
    } catch (error) {
        console.log('获取详情失败=>', error)
    }
}

const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key]
        }
    }
}

defineExpose({
    open,
    setFormData
})
</script>
