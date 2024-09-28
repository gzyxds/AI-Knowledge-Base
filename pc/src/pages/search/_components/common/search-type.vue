<template>
    <div class="mr-auto cursor-pointer">
        <el-popover
            v-model:visible="showTypeSelect"
            :trigger="trigger"
            :popper-style="{
                'border-radius': '15px'
            }"
            :show-arrow="false"
            transition="custom-popover"
            width="auto"
            :teleported="true"
            :hide-after="0"
            :placement="placement"
        >
            <template #reference>
                <slot name="item" v-bind="currentType"></slot>
            </template>
            <div>
                <div class="text-tx-secondary">搜索范围</div>
                <div class="flex mx-[-5px] mt-[10px]">
                    <template v-for="(item, index) in typeOptions">
                        <div
                            v-if="!item.disabled"
                            :key="index"
                            class="p-[12px] cursor-pointer rounded-[12px] mx-[5px] border-br-extra-light border-solid border"
                            :class="{
                                ' text-primary bg-primary-light-9 border-primary':
                                    currentType.value === item.value,
                                ' !text-tx-disabled !cursor-not-allowed':
                                    item.disabled
                            }"
                            @click="selectType(item)"
                        >
                            <div class="flex items-center">
                                <Icon :name="item.icon" />
                                <span class="ml-[6px]">{{ item.label }}</span>
                            </div>
                            <div
                                class="line-clamp-2 text-tx-secondary mt-[8px] text-sm"
                                :class="{
                                    '!text-tx-disabled': item.disabled
                                }"
                            >
                                {{ item.desc }}
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </el-popover>
    </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { TypeEnums, ModelEnums } from '../../searchEnums'

const props = withDefaults(
    defineProps<{
        model: string
        type?: string
        trigger: any
        placement: any
    }>(),
    {
        trigger: 'click',
        placement: 'bottom-start'
    }
)
const emit = defineEmits<{
    'update:type': [value: string]
}>()
const typeModel = useVModel(props, 'type', emit)

const typeOptions = computed(() => {
    return [
        {
            label: '全网搜索',
            value: TypeEnums.ALL,
            icon: 'local-icon-whole_network',
            desc: '在整个互联网中搜索'
        },
        {
            label: '文档搜索',
            value: TypeEnums.DOC,
            icon: 'local-icon-document',
            desc: '在互联网开放文库中搜索',
            disabled: props.model !== ModelEnums.STUDY
        },
        {
            label: '学术搜索',
            value: TypeEnums.SCHOLAR,
            icon: 'local-icon-science',
            desc: '在已发表的论文中搜索',
            disabled: props.model !== ModelEnums.STUDY
        }
    ]
})

const showTypeSelect = ref(false)
const currentType = computed<any>(() => {
    const current = typeOptions.value.find(
        (item) => item.value == typeModel.value
    )
    return current || {}
})
const selectType = (item: any) => {
    showTypeSelect.value = false
    if (item.disabled) return

    typeModel.value = item.value
}
</script>
