<template>
    <Popup
        ref="popRef"
        title="录入数据"
        width="800px"
        async
        @confirm="submit"
        @close="$emit('close')"
    >
        <div>
            <div class="grid grid-cols-2 gap-x-[20px]">
                <el-input
                    v-model="formData.question"
                    type="textarea"
                    placeholder="请输入文档内容，你可以理解为提问的问题（必填）"
                    rows="10"
                >
                </el-input>
                <el-input
                    v-model="formData.answer"
                    type="textarea"
                    placeholder="请填入补充内容，你可以理解为问题的答案"
                    rows="10"
                >
                </el-input>
            </div>
            <div class="mt-4">
                <Upload
                    v-model:files="formData.images"
                    type="image"
                    list-type="picture-card"
                    :limit="9"
                    multiple
                    show-file-list
                >
                    <div class="flex flex-col items-center justify-center">
                        <Icon name="el-icon-Plus" :size="20" />
                        <div class="text-info mt-2 text-sm">上传图片</div>
                    </div>
                </Upload>
                <div class="form-tips">最多上传9张</div>
            </div>
            <div class="mt-4">
                <UploadVideo v-model="video" size="80px"> </UploadVideo>
                <div class="form-tips">格式为MP4，大小不能超过20M</div>
            </div>
            <div class="mt-4">
                <Upload
                    v-model:files="formData.files"
                    type="file"
                    show-file-list
                >
                    <el-button>上传附件</el-button>
                    <template #tip>
                        <div class="el-upload__tip">
                            支持上传PDF、docx、excel、等文件格式
                        </div>
                    </template>
                </Upload>
            </div>
        </div>
    </Popup>
</template>

<script setup lang="ts">
import { itemDataImport, itemDataDetail, itemDataEdit } from '@/api/my_database'

const emits = defineEmits(['success', 'close'])

const popRef = shallowRef()

const video = ref('')
const formData = ref({
    kb_id: '',
    fd_id: '',
    question: '',
    answer: '',
    files: [],
    images: [],
    video: [],
    uuid: ''
})

watch(video, (value) => {
    formData.value.video = [{ url: value, name: '' }] as any
})
//提交数据
const { lockFn: submit } = useLockFn(async () => {
    console.log(formData.value)
    if (formData.value.uuid) {
        await itemDataEdit({ ...formData.value })
    } else {
        await itemDataImport({ ...formData.value })
    }

    emits('success')
})

//获取详情
const getDetail = async () => {
    const res = await itemDataDetail({ uuid: formData.value.uuid })
    // formData.value.question = res.question
    Object.keys(formData.value).map((item) => {
        //@ts-ignore
        formData.value[item] = res[item]
    })
    video.value = res.video[0]?.url || ''
    console.log(formData.value)
}

const open = (ids: any) => {
    popRef.value.open()
    ;[formData.value.kb_id, formData.value.fd_id, formData.value.uuid] = [
        ids.kb_id,
        ids.fd_id,
        ids.uuid || ''
    ]

    if (ids.hasOwnProperty('uuid')) {
        getDetail()
    }
}

defineExpose({ open })
</script>
