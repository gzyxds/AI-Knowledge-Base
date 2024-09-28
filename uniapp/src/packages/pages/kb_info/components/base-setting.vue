<template>
    <view class="h-full flex flex-col">
        <view class="flex-1 min-h-0">
            <scroll-view scroll-y class="h-full bg-white">
                <view class="px-[20rpx]">
                    <view>
                        <view>知识库封面</view>
                        <app-upload v-model="formData.image"></app-upload>
                    </view>
                    <view class="mt-2">
                        <view>知识库名称</view>
                        <view
                            class="border border-solid border-[#DCDFE6] rounded-lg p-[20rpx] mt-2"
                        >
                            <input v-model="formData.name" />
                        </view>
                    </view>
                    <view class="mt-2">
                        <view>知识库简介</view>
                        <view
                            class="border border-solid border-[#DCDFE6] rounded-lg p-[20rpx] mt-2"
                        >
                            <textarea
                                v-model="formData.intro"
                                placeholder="请用一句话描述知识库"
                            />
                        </view>
                    </view>
                    <view class="mt-2">
                        <view class="mb-2">向量模型</view>
                        <app-select
                            v-model="formData.embedding_model_id"
                            popupTitle="向量模型"
                            :dataLists="modelList.vectorModels"
                            placeholder="请选择"
                            name="alias"
                            value="id"
                            disabled
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
                    <view class="mt-2 mb-[130px]">
                        <view class="mb-2">文件处理模型</view>
                        <model-picker
                            v-model:id="formData.documents_model_id"
                            v-model:sub_id="formData.documents_model_sub_id"
                        >
                            <template #default="{ item }">
                                <view class="input flex">
                                    <view class="flex-1 min-w-0">
                                        <view class="line-clamp-1">
                                            <text v-if="item.alias">{{
                                                item.alias
                                            }}</text>
                                            <text class="text-[#888]" v-else
                                                >请选择</text
                                            >
                                            <text
                                                class="text-muted"
                                                v-if="
                                                    item.alias &&
                                                    item.price == '0'
                                                "
                                            >
                                                (免费)
                                            </text>
                                            <text
                                                class="text-muted"
                                                v-else-if="item.alias"
                                            >
                                                ({{
                                                    `消耗${item.price}${appStore.getTokenUnit}/1000字符`
                                                }})
                                            </text>
                                        </view>
                                    </view>
                                    <view
                                        class="text-muted flex flex-none mx-[20rpx]"
                                        v-if="formData.documents_model_id"
                                        @click.stop="
                                            ;(formData.documents_model_id = ''),
                                                (formData.documents_model_sub_id =
                                                    '')
                                        "
                                    >
                                        <u-icon name="close-circle" />
                                    </view>
                                    <view class="text-muted flex">
                                        <u-icon name="arrow-down" />
                                    </view>
                                </view>
                            </template>
                        </model-picker>
                    </view>
                </view>
            </scroll-view>
        </view>
        <view
            class="flex pt-[20rpx] left-0 w-full px-[10rpx] bg-white py-[20rpx]"
        >
            <button
                class="text-error flex-1 mr-2"
                :disabled="formData.power != 1"
                @click="del"
            >
                删除
            </button>
            <button
                class="bg-primary text-white flex-1"
                :disabled="KBInfo.power >= 2"
                @click="save"
            >
                保存
            </button>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useKB } from '../useKb'
import { editKB, delKB } from '@/api/kb'
import router from '@/router'
import { getAiModel } from '@/api/app'
import { isMiniProgram } from '@/utils/env'
import { useAppStore } from '@/stores/app'
//#ifdef H5
import wechat from '@/utils/wechat'
//#endif
const appStore = useAppStore()
const { KBInfo, KBId } = useKB()

const formData: any = ref({
    name: '', //库的名称
    image: '', //封面图标
    intro: '', //知识库简介
    power: 1, // 权限
    documents_model_id: '', //文件处理模型
    documents_model_sub_id: '', //文件处理模型
    embedding_model_id: '' //向量化的模型
})

// AI模型列表
const modelList = reactive<any>({
    chatModels: [],
    vectorModels: []
})

//保存
const save = async () => {
    await editKB({ ...formData.value })
}

//删除
const del = async () => {
    uni.showModal({
        title: '提示',
        content: '请确认是否删除！',
        success: async function (res) {
            if (res.confirm) {
                await delKB({ id: KBId.value })
                if (isMiniProgram) {
                    wechat.miniProgram.navigateBack()
                } else {
                    router.navigateBack()
                }
            } else if (res.cancel) {
                console.log('用户点击取消')
            }
        }
    })
}

const getModelList = async () => {
    const { vectorModels } = await getAiModel({ queryKey: 'modelLists' })
    modelList.vectorModels = vectorModels
}

onMounted(() => {
    formData.value = KBInfo.value
    getModelList()
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
