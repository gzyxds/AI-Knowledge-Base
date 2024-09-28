<template>
    <u-popup
        v-model="show"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
        @close="$emit('close')"
    >
        <view class="h-[70vh] flex flex-col mx-[20rpx]">
            <view
                class="text-xl py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                录入数据
            </view>
            <view></view>
            <scroll-view class="flex min-h-0 h-full" scroll-y>
                <view
                    class="p-[20rpx] border border-solid border-[#DCDFE6] rounded"
                >
                    <textarea
                        class="w-full"
                        placeholder="请输入文档内容，你可以理解为提问的问题（必填）"
                        v-model="formData.question"
                    />
                </view>
                <view
                    class="p-[20rpx] border border-solid border-[#DCDFE6] rounded mt-[20rpx]"
                >
                    <textarea
                        class="w-full"
                        placeholder="请输入补充内容，你可以理解为问题的答案"
                        v-model="formData.answer"
                    />
                </view>
                <view class="mt-4">
                    <app-upload
                        :maxCount="9"
                        v-model="formData.images"
                        returnType="object-array"
                    ></app-upload>
                    <span class="text-info">最多支持上传9张图</span>
                </view>
                <view class="mt-4">
                    <app-upload
                        :maxCount="1"
                        v-model="formData.video"
                        returnType="object-array"
                        accept="video"
                    ></app-upload>
                    <span class="text-info">格式为MP4，大小不能超过20M </span>
                </view>
                <view class="mt-4">
                    <app-upload-file v-model="formData.files">
                        <view>
                            <u-button>上传附件</u-button>
                        </view>
                    </app-upload-file>
                    <span class="text-info">
                        支持上传PDF、docx、excle等文件格式
                    </span>
                </view>
            </scroll-view>
            <view class="mt-2">
                <button @click="submit" class="bg-primary text-white">
                    保存
                </button>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { dataImport, dataUpdate, itemDataDetail } from '@/api/kb'

const emit = defineEmits(['submit', 'close'])

const show = ref(false)

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

// const props = withDefaults(
//     defineProps<{
//         modelValue: boolean
//     }>(),
//     {}
// )

// const emit = defineEmits<{
//     (event: 'update:modelValue', value: boolean): void
//     (event: 'close'): void
// }>()

// const show = computed({
//     get() {
//         return props.modelValue
//     },
//     set(value) {
//         emit('update:modelValue', value)
//     }
// })

const submit = async () => {
    if (formData.value.uuid) {
        await dataUpdate({ ...formData.value })
    } else {
        await dataImport({ ...formData.value })
    }
    emit('submit')
}

//获取数据
const getData = async () => {
    const res = await itemDataDetail({ uuid: formData.value.uuid })
    Object.keys(formData.value).map((item) => {
        //@ts-ignore
        formData.value[item] = res[item]
    })
}

const open = (kb_id: any, fd_id: any, uuid: any) => {
    show.value = true
    formData.value.kb_id = kb_id
    formData.value.fd_id = fd_id
    formData.value.uuid = uuid
    if (uuid != '') {
        getData()
    }
}

defineExpose({ open })
</script>

<style lang="scss" scoped></style>
