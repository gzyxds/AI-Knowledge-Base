<template>
    <u-popup
        v-model="show"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[80vh] flex flex-col">
            <view
                class="text-xl py-[28rpx] font-bold border-b border-solid border-light border-0 mx-[20rpx]"
            >
                文件导入
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view scroll-y class="h-full">
                    <view class="mx-[20rpx]">
                        <view>
                            <view
                                v-for="(item, index) in tabList"
                                :key="index"
                                class="rounded-lg border border-solid border-[#E2E6EA] p-[30rpx] mt-2"
                                :class="{ isSelect: item.type == isSelectType }"
                                @click="isSelectType = item.type"
                            >
                                <view>{{ item.name }}</view>
                                <view class="text-info text-xs mt-[10rpx]">{{
                                    item.describe
                                }}</view>
                            </view>
                        </view>
                        <div class="flex-1">
                            <Doc
                                v-if="ImportTypeEnum.DOC == isSelectType"
                                v-model="currentDate"
                            />
                            <Cvs
                                v-if="ImportTypeEnum.CVS == isSelectType"
                                v-model="currentDate"
                            />
                            <QASplit
                                v-if="ImportTypeEnum.QASplit == isSelectType"
                                v-model="currentDate"
                            />
                            <WebPage
                                v-if="ImportTypeEnum.WEB_PAGE == isSelectType"
                                v-model="currentDate"
                            />
                        </div>
                    </view>
                </scroll-view>
            </view>
            <view class="p-[20rpx]">
                <u-button @click="handleSubmit" type="primary">
                    确认导入
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { computed, reactive, ref } from 'vue'
import { ImportTypeEnum, IDataItem } from './import/hook'
import Doc from './import/doc.vue'
import Cvs from './import/csv.vue'
import QASplit from './import/QASplit.vue'
import WebPage from './import/web-page.vue'
import { useKB } from '../useKb'

const { importData, KBId } = useKB()

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

const show = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        seletFirst()
        emit('update:modelValue', value)
    }
})

//选中导入方式
const isSelectType = ref<number>(0)

const docData = ref<IDataItem[]>([])
const cvsData = ref<IDataItem[]>([])
const QASplitData = ref<IDataItem[]>([])
const webData = ref<IDataItem[]>([])
const tabList = computed(() => {
    const list = reactive([
        {
            name: '文档导入',
            type: ImportTypeEnum.DOC,
            describe: '选择文本文件，直接将其按分段进行处理',

            show: true,
            data: docData
        },
        {
            name: '问答对导入',
            type: ImportTypeEnum.CVS,
            describe: '批量导入问答对，效果最佳',

            show: true,
            data: cvsData
        },
        {
            name: '自动拆分问答对',
            type: ImportTypeEnum.QASplit,
            describe: '选择文本文件，让大模型自动生成问答对',

            show: true,
            data: QASplitData
        },

        {
            name: '网页解析',
            type: ImportTypeEnum.WEB_PAGE,
            describe: '输入网页链接，快速导入内容',
            //   show: Number(props.type) === 1,
            show: true,
            data: webData
        }
    ])
    return list.filter(({ show }) => show)
})

//选择导入方式
const selectImportType = (type: number) => {
    isSelectType.value = type
}

//加载第一个导入方式
const seletFirst = () => {
    selectImportType(tabList.value[0].type)
}

const currentDate = computed(
    () => tabList.value.find(({ type }) => type === isSelectType.value)?.data!
)

const handleSubmit = async () => {
    const selectTab = tabList.value.find(
        ({ type }) => type === isSelectType.value
    )
    const { data, type } = selectTab
    console.log(data, type)

    const params = {
        kb_id: KBId.value,
        method: type,
        documents: data
    }
    await importData({ ...params })
    emit('close')
}
</script>

<style lang="scss" scoped>
.isSelect {
    border: 1px solid var(--color-primary);
    color: var(--color-primary);
    background-color: var(--color-primary-light-9);
}
</style>
