<template>
    <div class="h-full flex">
        <InfoMenu
            v-model="defaultTab"
            :menu-list="tabList"
            :title="detailData.name"
            back-path="/application/layout/kb"
        />
        <div class="flex-1 min-w-0 overflow-auto pr-[16px] py-[16px]">
            <div class="bg-body h-full rounded-2xl">
                <div class="import-data h-full">
                    <!-- @vue-ignore -->
                    <component
                        :is="componentMap[defaultTab]"
                        :id="id"
                        :type="detailData.type"
                        @toImport="activeName = 2"
                        @success="activeName = 1"
                        @update="getDetail"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
// import datalist from './_components/detail/datalist.vue'
import dataStudy from './_components/data-study.vue'
// import importData from './_components/importData.vue'
import testData from './_components/testData.vue'
import setUp from './_components/setUp.vue'
import teamData from './_components/team-data.vue'

import { knowKnowledgeDetail } from '@/api/my_database'

const route = useRoute()
const router = useRouter()

const activeName = ref(1)
const id: number = route.query.id as any
const componentMap = {
    dataStudy,
    testData,
    teamData,
    setUp
}
const detailData = reactive({
    type: '',
    name: '',
    image: '',
    intro: '',
    owned: 1,
    power: 1,
    qa_length: ''
})

//tabs数据
const defaultTab = ref('dataStudy')

const tabList = [
    {
        name: '数据学习',
        icon: 'el-icon-Document',
        key: 'dataStudy'
    },
    {
        name: '搜索测试',
        key: 'testData',
        icon: 'el-icon-Search'
    },
    {
        name: '团队成员',
        key: 'teamData',
        icon: 'el-icon-User'
    },
    {
        name: '知识库设置',
        key: 'setUp',
        icon: 'el-icon-Setting'
    }
]

//获取数据库详情
const getDetail = async () => {
    const res = await knowKnowledgeDetail({ id })
    Object.keys(detailData).map((item: any) => {
        //@ts-ignore
        detailData[item] = res[item]
    })
    if (route.query.type) {
        defaultTab.value = route.query.type as string
    }
}

watch(() => defaultTab.value, (newVal) => {
    router.replace({
        path: '',
        query: {
            id: id,
            type: newVal
        }
    })
})

onMounted(() => {
    getDetail()
})

definePageMeta({
    auth: true,
    hiddenFooter: true,
    parentPath: '/application/layout/robot',
    activePath: '/application/layout/kb',
    title: '知识库编辑'
})

provide('knowDetail', detailData)
</script>

<style scoped lang="scss">
:deep(.el-card__body) {
    height: 100%;
}

.import-data {
    :deep(.el-tabs) {
        height: 100%;
        display: flex;
        flex-direction: column;

        // .el-tabs__header {
        //     margin-bottom: 0 !important;
        // }
        .el-tabs__content,
        .el-tab-pane {
            height: 100%;
            min-height: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
    }
}
</style>
