<template>
    <view class="flex items-center">
        <view
            v-if="chatModel.modelList.length"
            class="flex-1"
            @click="chatModel.show = true"
        >
            <slot :item="currentModel">
                <view
                    class="flex items-center p-[20rpx] text-sm"
                    v-if="currentModel.name"
                >
                    <view
                        class="leading-6 text-[#101010]"
                        :class="{
                            'line-clamp-1 max-w-[300rpx]': hasShowUnit
                        }"
                    >
                        {{ currentModel.alias }}/
                    </view>
                    <view
                        class="text-muted font-normal"
                        v-if="currentModel.alias && currentModel.price == '0'"
                    >
                        (免费)
                    </view>
                    <view
                        v-else-if="currentModel.alias"
                        class="text-muted leading-5 font-normal"
                        :class="{
                            'line-clamp-1 w-[90rpx]': hasShowUnit
                        }"
                    >
                        ({{
                            `消耗${currentModel.price}${appStore.getTokenUnit}/1000字符`
                        }})
                    </view>
                    <!--                <u-icon name="arrow-down" size="24rpx"></u-icon>-->
                    <image
                        class="w-[24rpx] h-[24rpx] ml-2"
                        src="@/static/images/icon/icon_toggle.png"
                    ></image>
                </view>
            </slot>
        </view>
        <u-popup
            v-model="chatModel.show"
            mode="bottom"
            :safe-area-inset-bottom="true"
            closeable
            border-radius="16"
        >
            <view class="p-[20rpx] text-lg font-bold"> 选择模型 </view>
            <scroll-view class="pb-[10rpx] h-[70vh]" scroll-y>
                <view class="px-[20rpx] pb-6 model-container">
                    <u-collapse
                        event-type="close"
                        :arrow="true"
                        :accordion="true"
                        ref="collapseRef"
                        @change="collapseChange"
                    >
                        <u-collapse-item
                            :index="index"
                            v-for="(item, index) in chatModel.modelList"
                            :key="index"
                            :class="{
                                'collapse-item--active': isActiveItem(index)
                            }"
                            :open="isActiveItem(index)"
                        >
                            <template #title>
                                <div>
                                    <view class="flex items-center py-2">
                                        <image
                                            v-if="item.logo"
                                            :src="item.logo"
                                            class="w-[60rpx] h-[60rpx]"
                                        />
                                        <text
                                            class="mx-2 leading-[24px] font-medium"
                                            >{{ item.name }}</text
                                        >
                                        <text
                                            v-if="item.is_free"
                                            class="bg-[#E3FFF2] text-[#23B571] text-xs px-[10rpx] py-[4rpx] leading-[40rpx] rounded-[6rpx]"
                                        >
                                            会员免费
                                        </text>
                                    </view>
                                    <view
                                        v-if="item.remarks"
                                        class="flex items-center text-xs text-muted font-normal mt-2 leading-[40rpx]"
                                    >
                                        {{ item.remarks }}
                                    </view>
                                </div>
                            </template>
                            <view class="flex-1 h-full">
                                <view
                                    class="mb-[20rpx] flex items-center justify-between"
                                    v-for="citem in item.models"
                                    :key="citem.id"
                                    @click="setModelAndClose(item.id, citem.id)"
                                >
                                    <view
                                        class="flex-1 min-w-0 flex items-center"
                                    >
                                        <view
                                            class="leading-6 text-main mr-2"
                                            >{{ citem.alias }}</view
                                        >
                                        <view
                                            v-if="
                                                citem.alias &&
                                                citem.price == '0'
                                            "
                                            class="text-tx-placeholder"
                                        >
                                            (免费)
                                        </view>
                                        <view
                                            v-else
                                            class="text-tx-secondary text-xs leading-5 font-normal"
                                        >
                                            {{
                                                `消耗${citem.price}${appStore.getTokenUnit}/1000字符`
                                            }}
                                        </view>
                                    </view>

                                    <view
                                        class="text-muted ml-1 mr-[2px]"
                                        v-if="subModel !== citem.id"
                                    >
                                        <u-image
                                            :src="IconUnSelect"
                                            width="32rpx"
                                            height="32rpx"
                                        ></u-image>
                                    </view>
                                    <view class="text-primary mt-[3px]" v-else>
                                        <u-icon
                                            name="checkmark-circle-fill"
                                            size="40rpx"
                                        ></u-icon>
                                    </view>
                                </view>
                            </view>
                        </u-collapse-item>
                    </u-collapse>
                </view>
            </scroll-view>
        </u-popup>
    </view>
</template>
<script lang="ts">
export default {
    options: {
        virtualHost: true,
        styleIsolation: 'shared'
    },
    externalClasses: ['class']
}
</script>
<script setup lang="ts">
import { getAiModel } from '@/api/app'
import { useAppStore } from '@/stores/app'
import IconUnSelect from '@/static/images/icon/icon_unselect.png'
import { computed, reactive, type PropType, ref, watch } from 'vue'

const props = defineProps({
    id: {
        type: String,
        default: ''
    },
    sub_id: {
        type: String,
        default: ''
    },
    setDefault: {
        type: Boolean,
        default: true
    },
    type: {
        type: String as PropType<'chatModels' | 'vectorModels'>,
        default: 'chatModels'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    hasHiddenUnit: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits(['update:id', 'update:sub_id', 'update:modelConfig'])
const model = computed({
    get() {
        return props.id
    },
    set(value) {
        emit('update:id', value)
    }
})
const subModel = computed({
    get() {
        return props.sub_id
    },
    set(value) {
        emit('update:sub_id', value)
    }
})
const appStore = useAppStore()

const activeName = ref<number | string | number[]>(-1)
// 聊天模型数据
const chatModel = reactive({
    modelList: [] as any[],
    show: false
})

const currentModel = computed(() => {
    return (
        chatModel.modelList
            .flatMap((item: any) => item.models)
            .find((item: any) => item.id === subModel.value) || {}
    )
})

const hasShowUnit = computed(() => {
    const flag = currentModel.value?.alias?.length > 10
    return props.hasHiddenUnit ? flag : false
})

const collapseChange = (name: number) => {
    activeName.value = name
}

// 获取聊天模型数据
const getChatModelFunc = async () => {
    try {
        const data = await getAiModel()
        chatModel.modelList = data[props.type]
        if (props.setDefault) {
            setDefaultModel()
        }
    } catch (error) {
        console.log('获取聊天模型数据错误=>', error)
    }
}

const setDefaultModel = () => {
    const defaultGroupIndex =
        chatModel.modelList.findIndex((item) => item.is_default) || 0
    const defaultModel = chatModel.modelList[defaultGroupIndex].models[0]
    if (defaultModel) {
        model.value = chatModel.modelList[defaultGroupIndex].id
        subModel.value = defaultModel.id
        activeName.value = defaultGroupIndex
    }
}

const setModelAndClose = (id: string, sub_id: string) => {
    model.value = id
    subModel.value = sub_id
    chatModel.show = false
}

watch(
    () => currentModel.value,
    (value) => {
        emit('update:modelConfig', value)
    }
)

const isActiveItem = (index: number) =>
    chatModel.modelList[index].models.some(
        (item: any) => item.id === subModel.value
    )

getChatModelFunc()
</script>

<style lang="scss">
.model-container {
    :deep() {
        .u-collapse-item {
            margin-top: 20rpx;
            padding: 0 20rpx;
            border-radius: 12rpx;
            border-width: 1px;
            border-style: solid;
            border-color: transparent;
            box-shadow: 0 1px 6px 0 rgba(230, 233, 237, 1);
        }
    }
    :deep() {
        .collapse-item--active {
            // #ifdef H5
            border-width: 1px;
            border-style: solid;
            @apply border-primary bg-primary-light-9;
            // #endif
            .u-collapse-item {
                border-width: 1px;
                border-style: solid;
                @apply border-primary bg-primary-light-9;
            }
        }
    }
}
</style>
