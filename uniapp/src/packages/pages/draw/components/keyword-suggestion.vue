<template>
    <u-popup
        v-model="visible"
        mode="bottom"
        :closeable="true"
        border-radius="20"
        :z-index="99999"
        safeAreaInsetBottom
        @close="emit('close')"
    >
        <view class="h-[90vh] flex flex-col min-h-0">
            <view
                class="text-xl font-medium text-black py-[26rpx] mx-[30rpx]"
                style="border-bottom: 1px solid #f3f3f3"
            >
                描述词推荐
            </view>
            <view class="flex flex-1 min-h-0">
                <scroll-view
                    class="h-full w-[162rpx] bg-page"
                    :scroll-y="true"
                >
                    <view class="bg-white">
                        <view
                            v-for="(item, index) in keywordCateList"
                            :key="index"
                            class="keyword-cate-item"
                            :class="{
                                'keyword-cate-item-active': index == current,
                                'keyword-cate-item-prev': index == current - 1,
                                'keyword-cate-item-next': index == current + 1,
                                'keyword-cate-item-has-prompt':
                                    currentCateNum.includes(index)
                            }"
                            @tap="changeCate(index)"
                        >
                            <text>{{ item.name }}</text>
                        </view>
                    </view>
                </scroll-view>
                <scroll-view class="flex-1 h-full" :scroll-y="true">
                    <template
                        v-if="
                            keywordPromptData.prompt.length ||
                            keywordPromptData.cate_prompt.length
                        "
                    >
                        <view
                            v-for="(
                                item, index
                            ) in keywordPromptData.cate_prompt"
                            :key="index"
                            class="keyword-container"
                        >
                            <view
                                class="py-[30rpx] text-base font-medium text-[#101010]"
                            >
                                {{ item.name }}
                            </view>
                            <view class="flex flex-wrap">
                                <view
                                    v-for="citem in item.prompt"
                                    :key="citem.text"
                                    class="keyword-item"
                                    :class="{
                                        'keyword-item-active':
                                            currentPrompt.includes(
                                                citem.prompt_en
                                            )
                                    }"
                                    @click="onChoicePrompt(citem.prompt_en)"
                                >
                                    {{ citem.prompt }}
                                </view>
                            </view>
                        </view>
                        <view
                            v-if="keywordPromptData.prompt.length"
                            class="keyword-container"
                        >
                            <view
                                class="py-[30rpx] text-base font-medium text-[#101010]"
                            >
                                其它
                            </view>
                            <view class="flex flex-wrap">
                                <view
                                    v-for="item in keywordPromptData.prompt"
                                    :key="item.text"
                                    class="keyword-item"
                                    :class="{
                                        'keyword-item-active':
                                            currentPrompt.includes(item.prompt_en)
                                    }"
                                    @click="onChoicePrompt(item.prompt_en)"
                                >
                                    {{ item.prompt }}
                                </view>
                            </view>
                        </view>
                    </template>
                    <view
                        v-else
                        class="h-full flex flex-col justify-center items-center"
                    >
                        <image
                            class="w-[300rpx] h-[300rpx]"
                            src="@/packages/static/images/drawing/empty.png"
                        ></image>
                        <view class="text-muted text-center"
                            >暂无关键词推荐</view
                        >
                    </view>
                </scroll-view>
            </view>
            <view
                class="bg-white z-10 w-full px-[30rpx] rounded-tl-md rounded-tr-md"
                style="box-shadow: 0 0 10rpx rgba(0, 0, 0, 0.2)"
            >
                <view class="pt-[20rpx]">
                    <view
                        class="flex justify-center items-center text-muted text-xs"
                    >
                        <text> 已选择 </text>
                        <text class="mx-1 text-primary">
                            {{ currentPrompt.length }}
                        </text>
                        <text>个Tag</text>
                    </view>
                </view>
                <view class="flex py-[20rpx]">
                    <view class="flex-1 mr-[10rpx]">
                        <u-button
                            type="primary"
                            :custom-style="{
                                width: '100%',
                                border: 'none',
                                color: '#333333',
                                background: '#F4F4F8'
                            }"
                            @click="onPromptAdd()"
                        >
                            添加到文本描述
                        </u-button>
                    </view>
                    <view class="flex-1 ml-[10rpx]">
                        <u-button
                            type="primary"
                            :custom-style="{
                                width: '100%'
                            }"
                            @click="onAlternatePrompt()"
                        >
                            替换当前文本
                        </u-button>
                    </view>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { nextTick, ref, watchEffect } from 'vue'
import { keywordCate, keywordPrompt } from '@/api/draw'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'close'): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: any
        model: string
    }>(),
    {
        modelValue: '',
        model: ''
    }
)

const visible = ref<boolean>(false)
const current = ref<number>(-1)
const keywordCateList = ref<any[]>([])
const keywordPromptData = ref<any>({
    prompt: [],
    cate_prompt: []
})
const currentPrompt = ref<string[]>([])
const currentCateNum = ref<number[]>([])

watchEffect(() => {
    const prompt = props.modelValue
    if (prompt == '') {
        currentPrompt.value = []
        currentCateNum.value = []
    }
})

const onChoicePrompt = (prompt: string) => {
    const index = currentPrompt.value.findIndex(
        (item: string) => item == prompt
    )
    if (index >= 0) {
        currentPrompt.value.splice(index, 1)
        currentCateNum.value.splice(index, 1)
        return
    }
    currentPrompt.value.push(prompt)
    currentCateNum.value.push(current.value)
}

const open = () => {
    visible.value = true
    getKeywordCate()
}

const changeCate = (index: number) => {
    if (current.value == index) {
        return
    }
    current.value = index
    getKeywordPrompt()
}

const getKeywordCate = async () => {
    try {
        keywordCateList.value = await keywordCate({
            model: props.model
        })
        await nextTick()
        current.value = 0
        await getKeywordPrompt()
    } catch (error) {
        console.log('获取关键词分类错误', error)
    }
}

const getKeywordPrompt = async () => {
    try {
        keywordPromptData.value = await keywordPrompt({
            model: props.model,
            id: keywordCateList.value[current.value].id
        })
    } catch (error) {
        console.log('获取关键词错误', error)
    }
}

// 添加关键词文本
const onPromptAdd = () => {
    visible.value = false
    if (props.modelValue.trim() == '') {
        const prompt = currentPrompt.value.length
            ? currentPrompt.value.join(',')
            : ''
        emit('update:modelValue', prompt)
    } else {
        const prompt = currentPrompt.value.length
            ? currentPrompt.value.join(',')
            : ''
        const keyword = `${props.modelValue} ${prompt ? `,${prompt}` : ''}`
        emit('update:modelValue', keyword)
    }
    currentPrompt.value = []
}

// 替换关键词文本
const onAlternatePrompt = () => {
    visible.value = false
    const prompt = currentPrompt.value.length
        ? currentPrompt.value.join(',')
        : ''
    emit('update:modelValue', prompt)
    currentPrompt.value = []
}

defineExpose({
    open
})
</script>

<style lang="scss" scoped>
.keyword-cate-item {
    @apply flex justify-center items-center text-[#9CA3AF] bg-page text-sm;
    height: 108rpx;
}
.keyword-cate-item-active {
    @apply text-[#101010] bg-white;
}
.keyword-cate-item-prev {
    border-radius: 0 0 8rpx 0;
}
.keyword-cate-item-next {
    border-radius: 0 8rpx 0 0;
}
.keyword-cate-item-has-prompt {
    position: relative;
    &::after {
        content: '';
        position: absolute;
        right: 10rpx;
        top: 50%;
        transform: translateY(-50%);
        width: 10rpx;
        height: 10rpx;
        border-radius: 50%;
        background-color: #ff5f57;
    }
}

.keyword-container {
    padding: 0 30rpx;
    .keyword-item {
        width: 162rpx;
        height: 68rpx;
        margin-right: 20rpx;
        margin-bottom: 20rpx;
        border-radius: 8rpx;
        @apply flex justify-center items-center bg-page text-sm text-content;
    }
    .keyword-item:nth-child(3n) {
        margin-right: 0;
    }
    .keyword-item-active {
        @apply text-primary bg-primary-light-9;
    }
}
</style>
