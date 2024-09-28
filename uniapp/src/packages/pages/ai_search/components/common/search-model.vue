<template>
    <view class="w-[550rpx]" v-if="mode === 'segmented'">
        <u-subsection
            :list="modelOptions"
            bg-color="#fff"
            :button-color="$theme.primaryColor"
            height="80"
            active-color="#fff"
            inactive-color="#333"
            @change="currentChange"
        >
            <template #item="{ item, isActive }">
                <view class="flex items-center">
                    <zui-svg-icon
                        width="28rpx"
                        :icon="item.icon"
                        :color="isActive ? '#fff' : $theme.primaryColor"
                    />
                    <text class="ml-[6rpx]">
                        {{ item.name }}
                    </text>
                </view>
            </template>
        </u-subsection>
    </view>
    <view class="flex items-center" v-else>
        <drop-down mode="down" v-model:show="showModel">
            <template v-slot="{ show }">
                <view class="flex items-center px-[20rpx] py-[14rpx]">
                    <zui-svg-icon
                        v-if="currentModel.icon"
                        width="28rpx"
                        :icon="currentModel.icon"
                        :color="$theme.primaryColor"
                    />
                    <text class="mx-[10rpx]">
                        {{ currentModel.name }}
                    </text>
                    <u-icon name="arrow-down-fill" size="14" />
                </view>
            </template>
            <template #menu>
                <view class="py-[8rpx] w-[250rpx]" @click.stop>
                    <view>
                        <SearchType
                            :model="item.value"
                            v-for="item in modelOptions"
                            v-model:type="typeModel"
                            :key="item.value"
                            mode="right"
                            @click-content="showModel = false"
                            :show="activeModel == item.value"
                            :show-mask="false"
                            @click-item="modelValue = activeModel"
                        >
                            <view
                                class="flex items-center justify-center py-[15rpx]"
                                :class="{
                                    ' bg-primary-light-9':
                                        activeModel === item.value
                                }"
                                @click.stop="activeModel = item.value"
                            >
                                <zui-svg-icon
                                    width="28rpx"
                                    :icon="item.icon"
                                    :color="$theme.primaryColor"
                                />
                                <text class="ml-[10rpx]">
                                    {{ item.name }}
                                </text>
                            </view>
                        </SearchType>
                    </view>
                </view>
            </template>
        </drop-down>
        <div class="border-l border-0 border-solid border-light pl-[16rpx]">
            <u-tag type="primary" size="mini" :text="currentType.label">
            </u-tag>
        </div>
    </view>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import SearchType from './search-type.vue'
import { ModelEnums, TypeEnums } from '../../useSearch'
import { computed, ref, watch } from 'vue'
const props = withDefaults(
    defineProps<{
        type?: string
        mode?: 'segmented' | 'dropdown'
        model: string
    }>(),
    {
        mode: 'segmented'
    }
)
const emit = defineEmits<{
    (event: 'update:model', value: string): void
    (event: 'update:type', value: string): void
}>()
const { type: typeModel, model: modelValue } = useVModels(props, emit)
const showModel = ref(false)
const modelOptions = [
    {
        name: '基础',
        value: ModelEnums.BASE,
        icon: 'search_base'
    },
    {
        name: '增强',
        value: ModelEnums.ENHANCE,
        icon: 'search_copilot',
        desc: '检索更多网页，提供更全面个性化答案'
    },
    {
        name: '研究',
        value: ModelEnums.STUDY,
        icon: 'search_research',
        desc: `结构更细致，内容更深入。自动总结大纲和图谱，答案更清晰`
    }
]

const activeModel = ref('')

const typeOptions = [
    {
        label: '全网',
        value: TypeEnums.ALL
    },
    {
        label: '文档',
        value: TypeEnums.DOC
    },
    {
        label: '学术',
        value: TypeEnums.SCHOLAR
    }
]

const currentType = computed<any>(() => {
    const current = typeOptions.find((item) => item.value == typeModel.value)
    return current || {}
})

watch(showModel, (value) => {
    if (value) {
        activeModel.value = modelValue.value
    }
})

const currentModel = computed<any>(() => {
    const current = modelOptions.find((item) => item.value == modelValue.value)
    return current || {}
})

const currentChange = (index: number) => {
    modelValue.value = modelOptions[index].value
}
</script>
