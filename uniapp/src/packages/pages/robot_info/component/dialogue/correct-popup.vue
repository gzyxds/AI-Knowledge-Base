<template>
    <u-popup
        v-model="showModel"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[80vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                修正问答
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="p-[20rpx]">
                        <u-form
                            :model="formData"
                            ref="uFormRef"
                            label-position="top"
                            :border-bottom="false"
                        >
                            <u-form-item
                                label="选择知识库"
                                prop="kb_id"
                                required
                            >
                                <view class="flex-1">
                                    <app-select
                                        v-model="formData.kb_id"
                                        popupTitle="知识库选择"
                                        :dataLists="kbLists"
                                        placeholder="请选择知识库"
                                        value="id"
                                    ></app-select>
                                </view>
                            </u-form-item>
                            <u-form-item label="提问问题" prop="ask" required>
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.ask"
                                        placeholder="请输入问题"
                                        :border="true"
                                        type="textarea"
                                        :height="200"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item label="问题答案" prop="reply" required>
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.reply"
                                        placeholder="请输入答案"
                                        :border="true"
                                        type="textarea"
                                        :height="300"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item label="上传图片">
                                <view class="flex-1 min-w-0">
                                    <app-upload
                                        v-model="images"
                                        :max-count="9"
                                    />
                                    <view class="text-muted">
                                        最多支持上传 9 张图
                                    </view>
                                </view>
                            </u-form-item>
                            <u-form-item label="上传视频">
                                <view class="flex-1">
                                    <app-upload
                                        :maxCount="1"
                                        v-model="formData.video"
                                        returnType="object-array"
                                        accept="video"
                                    ></app-upload>
                                    <view class="text-info">
                                        格式为MP4，大小不能超过20M
                                    </view>
                                </view>
                            </u-form-item>
                            <u-form-item label="上传附件">
                                <view class="flex-1 min-w-0">
                                    <app-upload-file
                                        v-model="formData.files"
                                        :fileExtname="[
                                            'doc',
                                            'docx',
                                            'pdf',
                                            'xls',
                                            'xlsx'
                                        ]"
                                    />
                                    <view class="text-muted">
                                        支持上传PDF、docx、excel、等文件格式
                                    </view>
                                </view>
                            </u-form-item>
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="flex p-[20rpx]">
                <view class="flex-1">
                    <u-button type="primary" @click="handleConfirm">
                        保存
                    </u-button>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { cloneDeep } from 'lodash'
import { useVModels } from '@vueuse/core'
import { computed, ref, shallowRef, watch } from 'vue'
import { robotRecordsCorrect } from '@/api/robot'
import { getKBList } from '@/api/kb'
const props = defineProps<{
    show: boolean
    data: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
}>()

const uFormRef = shallowRef()
const { show: showModel } = useVModels(props, emit)
const formData = ref({
    kb_id: '',
    ask: '',
    reply: '',
    images: [] as any[],
    files: [],
    video: []
})

const images = computed({
    get() {
        return formData.value.images.map(({ url }) => url)
    },
    set(value) {
        formData.value.images = value.map((url) => ({ url }))
    }
})
const kbLists = ref([])
const getKbData = async () => {
    const { lists = [] } = await getKBList({
        page_type: 0
    })
    kbLists.value = lists
}
getKbData()
const formRules = {
    kb_id: [
        {
            required: true,
            message: '请选择知识库'
        }
    ],
    ask: [
        {
            required: true,
            message: '请输入问题'
        }
    ],
    reply: [
        {
            required: true,
            message: '请输入答案'
        }
    ]
}

const handleConfirm = () => {
    uFormRef.value.validate(async (valid: boolean) => {
        if (valid) {
            await robotRecordsCorrect(formData.value)
            setTimeout(() => {
                showModel.value = false
            }, 1000)
        }
    })
}

watch(showModel, (value) => {
    if (value) {
        setTimeout(() => {
            uFormRef.value.setRules(formRules)
        })
    }
})

watch(
    () => props.data,
    (value) => {
        Object.assign(formData.value, cloneDeep(value))
    }
)
</script>
