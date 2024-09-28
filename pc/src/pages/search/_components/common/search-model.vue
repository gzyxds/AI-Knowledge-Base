<template>
    <DefineTemplate v-slot="{ item, select }">
        <div class="flex items-center justify-center">
            <span
                :class="{
                    'text-primary': !select
                }"
            >
                <Icon size="15" :name="item.icon"> </Icon>
            </span>

            <div class="ml-1">{{ item.label }}</div>
        </div>
    </DefineTemplate>
    <el-segmented
        v-if="mode == 'segmented'"
        v-model="modelValue"
        :options="modelOptions"
        :style="{
            width: `${modelOptions.length * 90}px`,
            '--el-border-radius-base': '10px',
            '--el-segmented-color': 'var(--el-text-color-primary)'
        }"
    >
        <template #default="{ item }">
            <el-tooltip
                effect="dark"
                :content="item.desc"
                :disabled="!item.desc"
                placement="top"
            >
                <div class="py-[10px]">
                    <ReuseTemplate
                        :item="item"
                        :select="item.value == modelValue"
                    />
                </div>
            </el-tooltip>
        </template>
    </el-segmented>
    <div class="flex items-center" v-else>
        <el-popover
            placement="bottom"
            trigger="click"
            :width="120"
            :popper-style="{
                minWidth: '120px',
                padding: 0
            }"
            v-model:visible="showModel"
        >
            <template #reference>
                <div class="flex items-center cursor-pointer">
                    <span class="flex text-primary">
                        <Icon :name="currentModel.icon" />
                    </span>

                    <span class="px-[6px]">
                        {{ currentModel.label }}
                    </span>
                    <Icon name="el-icon-CaretBottom" />
                </div>
            </template>
            <div class="py-[10px]">
                <template v-for="item in modelOptions" :key="item.value">
                    <div
                        :class="{
                            'bg-primary-light-9': hoverModel == item.value
                        }"
                        @mouseover="hoverModel = item.value"
                    >
                        <SearchType
                            :model="item.value"
                            v-model:type="typeModel"
                            @update:type="modelValue = item.value"
                            trigger="hover"
                            placement="right"
                        >
                            <template #item="{ icon, label }">
                                <div class="py-[6px]">
                                    <ReuseTemplate
                                        class="px-[10px]"
                                        :item="item"
                                        :select="false"
                                    />
                                </div>
                            </template>
                        </SearchType>
                    </div>
                </template>
            </div>
        </el-popover>

        <div class="border-l border-solid border-br-light ml-[8px] pl-[8px]">
            <el-tag type="primary" size="small">
                {{ currentType.label }}
            </el-tag>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import SearchType from './search-type.vue'
import { ModelEnums, TypeEnums } from '../../searchEnums'
import { watch } from 'vue'
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
    'update:model': [value: string]
    'update:type': [value: string]
}>()
const [DefineTemplate, ReuseTemplate] = useTemplate<{
    item: any
    select: boolean
}>()
const { type: typeModel, model: modelValue } = useVModels(props, emit)
const showModel = ref(false)
const hoverModel = ref('')

const modelOptions = [
    {
        label: '基础',
        value: ModelEnums.BASE,
        icon: 'local-icon-search_base'
    },
    {
        label: '增强',
        value: ModelEnums.ENHANCE,
        icon: 'local-icon-search_copilot',
        desc: '检索更多网页，提供更全面个性化答案'
    },
    {
        label: '研究',
        value: ModelEnums.STUDY,
        icon: 'local-icon-search_research',
        desc: `结构更细致，内容更深入。自动总结大纲和图谱，答案更清晰`
    }
]
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
const currentModel = computed<any>(() => {
    const current = modelOptions.find((item) => item.value == modelValue.value)
    return current || {}
})

watch(showModel, () => {
    hoverModel.value = ''
})
</script>
