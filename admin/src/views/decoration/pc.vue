<template>
    <div class="decoration-pages">
        <div class="flex flex-1 min-w-0 items-start overflow-x-auto overflow-y-hidden">
            <Preview
                class="flex flex-1 min-w-0 h-full"
                :page-data="pageData[activeMenu].data"
                v-model:index="selectWidgetIndex"
            />
            <div class="h-full py-4">
                <prop-setting :title="getSelectWidget.title">
                    <template #default>
                        <component
                            v-model:is-show="getSelectWidget.isShow"
                            v-model:prop="getSelectWidget.prop"
                            :is="widgets[getSelectWidget?.name]?.prop"
                        />
                    </template>
                </prop-setting>
            </div>
        </div>
        <footer-btns :fixed="true" v-perms="['decorate.page/save']">
            <el-button type="primary" @click="setData">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup name="decorationPc">
import { getDecoratePages, setDecoratePages } from '@/api/decoration'
import Preview from './component/preview-pc.vue'
import PropSetting from './component/prop-setting.vue'
import widgets from './component/widgets-pc'
import { getNonDuplicateID } from '@/utils/util'

const route = useRoute()

const generatePageData = (widgetNames: string[]) => {
    return widgetNames.map((widgetName) => {
        const options = {
            id: getNonDuplicateID(),
            ...(widgets[widgetName]?.config() || {})
        }
        return options
    })
}

const pageData = ref([
    {
        id: 1,
        type: 1,
        name: 'pc首页装修',
        data: [] as any[]
    },
    {
        id: 5,
        type: 5,
        name: 'pc创作装修',
        data: [] as any[]
    },
    {
        id: 6,
        type: 6,
        name: 'pc智能体广场',
        data: [] as any[]
    }
])

const activeMenu = ref(0)
const selectWidgetIndex = ref(0)

const getSelectWidget = computed(() => {
    return pageData.value[activeMenu.value].data[selectWidgetIndex.value] || {}
})
const getData = async () => {
    const data = await getDecoratePages({ id: pageData.value[activeMenu.value].id })
    if (!data.data) {
        pageData.value[activeMenu.value].data = generatePageData(['header', 'title', 'intro', 'guide'])
        return
    }
    pageData.value[activeMenu.value] = {
        ...data,
        data: JSON.parse(data.data)
    }
}

const setData = async () => {
    await setDecoratePages({
        ...pageData.value[activeMenu.value],
        data: JSON.stringify(pageData.value[activeMenu.value].data)
    })
    getData()
}

onMounted(() => {
    activeMenu.value = route.query.type as unknown as number || 0
    console.log(route.query)
    getData()
})
</script>
<style lang="scss" scoped>
.decoration-pages {
    position: absolute;
    inset: 0;
    @apply flex flex-col;
}
</style>
