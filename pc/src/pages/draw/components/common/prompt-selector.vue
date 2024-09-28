<template>
    <div class="keyword-dialog">
        <div class="dialog__trigger" @click="open">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>

        <el-dialog
            v-model="visible"
            width="870px"
            :align-center="true"
            style="border-radius: 12px"
        >
            <template #header>
                <div class="text-xl font-medium text-[#101010] dark:text-white">
                    描述词推荐
                </div>
            </template>

            <div class="flex">
                <ElScrollbar class="w-[110px] bg-page-base" height="500px">
                    <div class="bg-[var(--el-bg-color)]">
                        <div
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
                            @click="changeCate(index)"
                        >
                            <span>{{ item.name }}</span>
                        </div>
                    </div>
                </ElScrollbar>
                <ElScrollbar class="flex-1" height="500px">
                    <template
                        v-if="
                            keywordPromptData.prompt.length ||
                            keywordPromptData.cate_prompt.length
                        "
                    >
                        <div
                            v-for="(
                                item, index
                            ) in keywordPromptData.cate_prompt"
                            :key="index"
                            class="keyword-container"
                        >
                            <div
                                class="py-[15px] text-base font-medium text-[#101010] dark:text-white"
                            >
                                {{ item.name }}
                            </div>
                            <div
                                v-for="citem in item.prompt"
                                :key="citem.text"
                                class="keyword-item"
                                :class="{
                                    'keyword-item-active':
                                        currentPrompt.includes(citem.prompt_en)
                                }"
                                @click="onChoicePrompt(citem.prompt_en)"
                            >
                                {{ citem.prompt }}
                            </div>
                        </div>
                        <div
                            v-if="keywordPromptData.prompt.length"
                            class="keyword-container"
                        >
                            <div
                                class="py-[15px] text-base font-medium text-[#101010] dark:text-white"
                            >
                                其它
                            </div>
                            <div
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
                            </div>
                        </div>
                    </template>
                    <!--  空状态  -->
                    <div
                        v-else
                        class="flex items-center justify-center w-full h-full"
                    >
                        <el-result title="" sub-title="暂无关键词推荐"
                            >>
                            <template #icon>
                                <el-image
                                    class="w-[200px] h-[200px]"
                                    :src="PromptEmpty"
                                />
                            </template>
                        </el-result>
                    </div>
                </ElScrollbar>
            </div>

            <template #footer>
                <div class="flex justify-between">
                    <div
                        class="flex justify-center items-center text-tx-secondary text-base"
                    >
                        <span> 已选择 </span>
                        <span class="mx-1 text-primary">
                            {{ currentPrompt.length }}
                        </span>
                        <span>个Tag</span>
                    </div>
                    <div class="dialog-footer">
                        <el-button type="primary" @click="onPromptAdd()">
                            添加到文本描述
                        </el-button>
                        <el-button
                            type="primary"
                            class="ml-[10px]"
                            :plain="true"
                            @click="onAlternatePrompt()"
                        >
                            替换当前文本描述
                        </el-button>
                    </div>
                </div>
            </template>
        </el-dialog>
    </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import { keywordCate, keywordPrompt } from '@/api/draw'
import PromptEmpty from '@/assets/image/draw/drawing_empty.png'
import { checkUserLogin, formData } from './../../hooks/useDrawEffect'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)

const visible = ref(false)
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

// 选择关键词
const onChoicePrompt = (text: string) => {
    const index = currentPrompt.value.findIndex((item: string) => item == text)
    if (index >= 0) {
        currentPrompt.value.splice(index, 1)
        currentCateNum.value.splice(index, 1)
        return
    }
    currentPrompt.value.push(text)
    currentCateNum.value.push(current.value)
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

const open = () => {
    if (checkUserLogin()) return
    visible.value = true
}

const changeCate = (index: number) => {
    if (current.value == index) {
        return
    }
    console.log(index)
    current.value = index
    getKeywordPrompt()
}

const getKeywordCate = async () => {
    try {
        await nextTick()
        keywordCateList.value = await keywordCate({
            model: formData.value.draw_api
        })
        current.value = 0
        await getKeywordPrompt()
    } catch (error) {
        console.log('获取关键词分类错误', error)
    }
}

const getKeywordPrompt = async () => {
    try {
        keywordPromptData.value = await keywordPrompt({
            model: formData.value.draw_api,
            id: keywordCateList.value[current.value].id
        })
    } catch (error) {
        console.log('获取关键词错误', error)
    }
}

onMounted(() => {
    getKeywordCate()
})
</script>

<style lang="scss" scoped>
.bg-page-base {
    background-color: var(--el-bg-color-page);
}
.keyword-dialog {
    :deep(.el-dialog__header) {
        padding-bottom: 20px !important;
        border-bottom: 1px solid var(--el-bg-color-page);
    }
    :deep(.el-dialog__body) {
        padding: 0 !important;
    }
}

.keyword-cate-item {
    @apply flex justify-center items-center text-[#9CA3AF] bg-page-base text-sm;
    height: 48px;
    cursor: pointer;
}
.keyword-cate-item-active {
    background-color: var(--el-bg-color);
    @apply text-[#101010] dark:text-white;
}
.keyword-cate-item-prev {
    border-radius: 0 0 8px 0;
}
.keyword-cate-item-next {
    border-radius: 0 8px 0 0;
}
.keyword-cate-item-has-prompt {
    position: relative;
    &::after {
        content: '';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: #ff5f57;
    }
}

.keyword-container {
    padding: 0 20px;
    .keyword-item {
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
        padding: 6px 15px;
        border-radius: 4px;
        cursor: pointer;
        @apply bg-page-base text-sm text-tx-regular;
    }
    .keyword-item-active {
        @apply text-primary bg-primary-light-9;
    }
}
</style>
