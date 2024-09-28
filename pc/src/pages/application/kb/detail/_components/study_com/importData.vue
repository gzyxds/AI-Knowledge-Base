<template>
    <div class="h-full flex flex-col relative">
        <div class="pb-[20px] flex items-center font-bold cursor-pointer">
            <div @click="toBack" class="flex items-center">
                <Icon name="el-icon-Back" size="16"></Icon>
                <span class="ml-2">文件导入</span>
            </div>
        </div>
        <div class="flex">
            <div
                v-for="(item, index) in tabList"
                :key="index"
                class="unselect w-[290px] p-[16px] text-center rounded-md cursor-pointer mr-4"
                :class="{ isselect: item.type == isSelectType }"
                @click="selectImportType(item.type)"
            >
                <div>{{ item.name }}</div>
                <div class="text-info text-[14px]">
                    {{ item.describe }}
                </div>
            </div>
        </div>
        <div class="flex-1">
            <template v-for="(item, index) in tabList" :key="item.type">
                <component
                    :is="item.component"
                    v-show="item.type == isSelectType"
                    v-model="item.data"
                    :type="props.type"
                />
            </template>
        </div>
        <div v-if="tabList.length > 0">
            <FooterBtns :fixed="!appStore.isMobile">
                <el-button type="primary" @click="handleSubmit">
                    保存
                </el-button>
            </FooterBtns>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ImportTypeEnum } from '../import/hook'
import type { IDataItem, IManualQAData } from '../import/hook'
import Manual from './import/manual.vue'
import Cvs from '../import/cvs.vue'
import Doc from '../import/doc.vue'
import QASplit from '../import/QASplit.vue'
import ManualDoc from './import/manual-doc.vue'
import WebPage from '../import/web-page.vue'
import { fileDataimport } from '@/api/my_database'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
const emits = defineEmits(['success', 'back'])
const props = defineProps({
    id: {
        type: Number,
        default: 0
    },
    type: {
        type: String,
        default: ''
    }
})

//选中导入方式
const isSelectType = ref<number>(0)

// //录入数据
// const manualDocData = ref<IDataItem>({
//   question: '',
//   answer: '',
//   source: '手动输入'
// })

// const manualData = ref<IManualQAData>({
//   files: [],
//   images: [],
//   question: '',
//   answer: '',
//   source: '手动输入'
// })
const docData = ref<IDataItem[]>([])
const cvsData = ref<IDataItem[]>([])
const QASplitData = ref<IDataItem[]>([])
const webData = ref<IDataItem[]>([])

//录入类型
const tabList = computed(() => {
    const list = reactive([
        // {
        //   name: '手动输入',
        //   type: ImportTypeEnum.MANUAL,
        //   component: ManualDoc,
        //   describe: '手动输入内容，是最精准的数据',
        //   show: Number(props.type) === 1,
        //   data: manualDocData
        // },
        // {
        //   name: '手动输入',
        //   type: ImportTypeEnum.MANUAL,
        //   describe: '手动输入问答对，是最精准的数据',
        //   component: Manual,
        //   show: Number(props.type) === 2,
        //   data: manualData
        // },
        {
            name: '文档导入',
            type: ImportTypeEnum.DOC,
            describe: '选择文本文件，直接将其按分段进行处理',
            component: Doc,
            //   show: Number(props.type) === 1,
            show: true,
            data: docData
        },
        {
            name: '问答对导入',
            type: ImportTypeEnum.CVS,
            describe: '批量导入问答对，效果最佳',
            component: Cvs,
            //   show: Number(props.type) === 2,
            show: true,
            data: cvsData
        },
        {
            name: '自动拆分问答对',
            type: ImportTypeEnum.QASplit,
            describe: '选择文本文件，让大模型自动生成问答对',
            component: QASplit,
            //   show: Number(props.type) === 2,
            show: true,
            data: QASplitData
        },

        {
            name: '网页解析',
            type: ImportTypeEnum.WEB_PAGE,
            describe: '输入网页链接，快速导入内容',
            component: WebPage,
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

//提交
const { lockFn: handleSubmit } = useLockFn(async () => {
    const selectTab = tabList.value.find(
        ({ type }) => type === isSelectType.value
    )
    console.log(selectTab?.data)

    // const params: any = {
    //   kb_id: props.id,
    //   method: selectTab?.type,
    //   documents: [],
    //   images: [],
    //   files: []
    // }
    // const data = selectTab?.data || ({} as any)
    const { data, type } = selectTab
    const params = {
        kb_id: props.id,
        method: type,
        documents: data
    }
    await fileDataimport({ ...params })
    toBack()
    //   if (isArray(data)) {
    //     params.documents = data
    //   } else {
    //     const { question, answer, source, images, files } = data
    //     params.documents = [{ question, answer, source }]
    //     params.images = images
    //     params.files = files
    //   }
    //   params.documents = params.documents.filter(
    //     ({ question, answer }: any) => question || answer
    //   )
    //   if (!params.documents.length) return feedback.msgError('数据为空，请添加数据')
    //   try {
    //     const step = 1000
    //     feedback.loading('正在上传中，请稍后。。。')
    //     for (let i = 0; i < params.documents.length; i += step) {
    //       await itemDataimport({
    //         ...params,
    //         documents: params.documents.slice(i, i + step)
    //       })
    //     }
    //     feedback.closeLoading()
    //     feedback.msgSuccess('导入成功！')
    //     emits('success')
    //     resetData()
    //   } catch (error) {
    //     feedback.closeLoading()
    //     console.log(error)
    //   }
})
//重置数据
const resetData = () => {
    tabList.value.forEach((item) => {
        item.data = []
    })
}

//返回
const toBack = () => {
    emits('back')
}

onMounted(() => {
    seletFirst()
})
</script>

<style scoped lang="scss">
.unselect {
    border: 1px solid #e2e6ea;
}

.isselect {
    border: 1px solid var(--el-color-primary);
    @apply bg-primary-light-9;
}
</style>
