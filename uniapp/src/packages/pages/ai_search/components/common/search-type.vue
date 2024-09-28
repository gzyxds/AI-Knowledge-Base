<template>
    <view class="">
        <drop-down :mode="mode" v-model:show="showType" :show-mask="showMask">
            <template v-slot="{ show }">
                <slot v-bind="currentType"></slot>
            </template>

            <template #menu>
                <view
                    class="p-[20rpx] mb-[-20rpx]"
                    @click.stop="emit('click-content')"
                >
                    <template v-for="(item, index) in typeOptions">
                        <view
                            v-if="!item.disabled"
                            :key="index"
                            class="p-[24rpx] rounded-[12rpx] border-light border-solid border mb-[20rpx]"
                            :class="{
                                ' text-primary bg-primary-light-9 border-primary':
                                    currentType.value === item.value
                            }"
                            @click="selectType(item.value)"
                        >
                            <view class="flex items-center">
                                <zui-svg-icon
                                    width="28rpx"
                                    :icon="item.icon"
                                    :color="
                                        currentType.value === item.value
                                            ? $theme.primaryColor
                                            : '#333'
                                    "
                                />
                                <span class="ml-[12rpx]">{{ item.label }}</span>
                            </view>
                            <view
                                class="line-clamp-2 text-muted mt-[16rpx] text-sm"
                            >
                                {{ item.desc }}
                            </view>
                        </view>
                    </template>
                </view>
            </template>
        </drop-down>
    </view>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { ModelEnums, TypeEnums } from '../../useSearch'
import { ref, computed, watch } from 'vue'
const props = withDefaults(
    defineProps<{
        model: string
        type?: string
        mode: string
        show: boolean
        showMask: boolean
    }>(),
    {
        type: '',
        mode: 'down',
        showMask: true
    }
)
const emit = defineEmits<{
    (event: 'update:type', value: string): void
    (event: 'click-content'): void
    (event: 'click-item'): void
}>()
const typeModel = useVModel(props, 'type', emit)
const showType = ref(false)
watch(
    () => props.show,
    (value) => {
        showType.value = value
    },
    {
        immediate: true
    }
)
const typeOptions = computed(() => [
    {
        label: '全网搜索',
        value: TypeEnums.ALL,
        icon: 'whole_network',
        desc: '在整个互联网中搜索'
    },
    {
        label: '文档搜索',
        value: TypeEnums.DOC,
        icon: 'document',
        desc: '在互联网开放文库中搜索',
        disabled: props.model !== ModelEnums.STUDY
    },
    {
        label: '学术搜索',
        value: TypeEnums.SCHOLAR,
        icon: 'science',
        desc: '在已发表的论文中搜索',
        disabled: props.model !== ModelEnums.STUDY
    }
])

const showTypeSelect = ref(false)
const currentType = computed<any>(() => {
    const current = typeOptions.value.find(
        (item) => item.value == typeModel.value
    )
    return current || {}
})

const selectType = (value: string) => {
    emit('click-item')
    showTypeSelect.value = false
    typeModel.value = value
    if (!props.show) {
        showType.value = false
    }
}
</script>
