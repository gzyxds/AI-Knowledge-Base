<template>
    <u-popup v-model="show" closeable border-radius="16" mode="bottom">
        <view class="h-[70vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                创建知识库
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
                                label="知识库封面"
                                prop="image"
                                required
                            >
                                <view class="mx-[-10rpx] flex-1 min-w-0">
                                    <app-upload v-model="formData.image" />
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="知识库名称"
                                prop="name"
                                required
                            >
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.name"
                                        placeholder="请输入知识库名称"
                                        :border="true"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item label="知识库简介" prop="intro">
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.intro"
                                        type="textarea"
                                        :height="200"
                                        placeholder="请用一句话描述知识库"
                                        :border="true"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="向量模型"
                                prop="embedding_model_id"
                                required
                            >
                                <view class="flex-1">
                                    <app-select
                                        v-model="formData.embedding_model_id"
                                        popupTitle="向量模型"
                                        :dataLists="modelList.vectorModels"
                                        placeholder="请选择"
                                        name="alias"
                                        value="id"
                                    >
                                        <template #label="{ item }">
                                            <view>
                                                <view>
                                                    {{ item.alias }}
                                                </view>
                                            </view>
                                        </template>
                                    </app-select>
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="文件处理模型"
                                prop="documents_model_id"
                                required
                            >
                                <view class="flex-1">
                                    <model-picker
                                        :setDefault="false"
                                        v-model:id="formData.documents_model_id"
                                        v-model:sub_id="
                                            formData.documents_model_sub_id
                                        "
                                    >
                                        <template #default="{ item }">
                                            <view class="input flex">
                                                <view class="flex-1 min-w-0">
                                                    <view class="line-clamp-1">
                                                        <text
                                                            v-if="item.alias"
                                                            >{{
                                                                item.alias
                                                            }}</text
                                                        >
                                                        <text
                                                            class="text-[#888]"
                                                            v-else
                                                            >请选择</text
                                                        >
                                                        <text
                                                            class="text-muted"
                                                            v-if="
                                                                item.alias &&
                                                                item.price ==
                                                                    '0'
                                                            "
                                                        >
                                                            (免费)
                                                        </text>
                                                        <text
                                                            class="text-muted"
                                                            v-else-if="
                                                                item.alias
                                                            "
                                                        >
                                                            ({{
                                                                `消耗${item.price}${appStore.getTokenUnit}/1000字符`
                                                            }})
                                                        </text>
                                                    </view>
                                                </view>
                                                <view
                                                    class="text-muted flex flex-none mx-[20rpx]"
                                                    v-if="
                                                        formData.documents_model_id
                                                    "
                                                    @click.stop="
                                                        ;(formData.documents_model_id =
                                                            ''),
                                                            (formData.documents_model_sub_id =
                                                                '')
                                                    "
                                                >
                                                    <u-icon
                                                        name="close-circle"
                                                    />
                                                </view>
                                                <view class="text-muted flex">
                                                    <u-icon name="arrow-down" />
                                                </view>
                                            </view>
                                        </template>
                                    </model-picker>
                                </view>
                            </u-form-item>
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="flex p-[20rpx]">
                <view class="flex-1">
                    <u-button type="primary" @click="handelSave">保存</u-button>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { addKB } from '@/api/kb'
import { watch, computed, ref, shallowRef, reactive } from 'vue'
import { getAiModel } from '@/api/app'
import { useRouter } from 'uniapp-router-next'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
const props = withDefaults(
    defineProps<{
        modelValue: boolean
    }>(),
    {}
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
    (event: 'close'): void
}>()
const uFormRef = shallowRef()
const show = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

// AI模型列表
const modelList = reactive<any>({
    chatModels: [],
    vectorModels: []
})

const alias = reactive({
    document: '',
    embedding: ''
})

const formData = ref({
    image: '',
    name: '',
    intro: '',
    embedding_model_id: '',
    documents_model_id: '',
    documents_model_sub_id: ''
})

const rules = reactive({
    image: [
        {
            required: true,
            message: '请选择知识库图标',
            trigger: ['change']
        }
    ],
    name: [
        {
            required: true,
            message: '请输入知识库名称',
            trigger: ['change']
        }
    ],
    embedding_model_id: [
        {
            required: true,
            message: '请选择向量模型'
        }
    ],
    documents_model_id: [
        {
            required: true,
            message: '请选择文件处理模型'
        }
    ]
})

const getModelList = async () => {
    const { vectorModels } = await getAiModel({ queryKey: 'modelLists' })
    modelList.vectorModels = vectorModels
}

const router = useRouter()
const handelSave = () => {
    uFormRef.value.validate(async (valid: boolean) => {
        if (valid) {
            const { id } = await addKB(formData.value)
            router.navigateTo({
                path: '/packages/pages/kb_info/kb_info',
                query: {
                    id
                }
            })
            emit('close')
        }
    })
}

watch(show, (value) => {
    if (value) {
        setTimeout(async () => {
            await getModelList()
            uFormRef.value.setRules(rules)
        })
        formData.value = {
            image: '',
            name: '',
            intro: '',
            documents_model_id: '',
            documents_model_sub_id: '',
            embedding_model_id: ''
        }
    }
})
</script>

<style lang="scss" scoped>
.input {
    min-height: 70rpx;
    width: 100%;
    border-radius: 8rpx;
    line-height: 50rpx;
    border: 1px solid var(--color-light, #e5e5e5);
    &-placeholder {
        color: rgb(136, 136, 136);
    }
    padding: 10rpx 20rpx;
    box-sizing: border-box;
}
</style>
