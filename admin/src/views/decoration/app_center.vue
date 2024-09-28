<template>
    <div class="decoration-pages min-w-[1100px]">
        <div class="flex h-full items- justify-between">
            <Menu v-model="activeMenu" :menus="menus" />
            <Preview
                class="flex-1 scroll-view-content"
                v-model="selectWidgetIndex"
                :pageData="getPageData"
            />
            <attr-setting class="bg-white" :widget="getSelectWidget" />
        </div>
        <footer-btns class="mt-4" :fixed="true" v-perms="['decorate.page/save']">
            <el-button type="primary" @click="setData">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup name="appDecorationPages">
import Menu from './component/pages/menu.vue'
import Preview from './component/pages/preview.vue'
import AttrSetting from './component/pages/attr-setting.vue'
import widgets from './component/widgets'
import { getDecoratePages, setDecoratePages } from '@/api/decoration'
import { getNonDuplicateID } from '@/utils/util'
enum pagesTypeEnum {
    APP_CENTER = 11
}

const generatePageData = (widgetNames: string[]) => {
    return widgetNames.map((widgetName) => {
        const options = {
            id: getNonDuplicateID(),
            ...(widgets[widgetName]?.options() || {})
        }
        return options
    })
}

const menus = reactive([
    {
        id: pagesTypeEnum.APP_CENTER,
        type: pagesTypeEnum.APP_CENTER,
        name: '应用中心',
        pageData: generatePageData(['ai-app-center'])
    }
])

const activeMenu = ref(pagesTypeEnum.APP_CENTER)
const selectWidgetIndex = ref(0)
const getPageData = computed(() => {
    return menus.find((item) => item.type == activeMenu.value)?.pageData || []
})
const getSelectWidget = computed(() => {
    return getPageData.value[selectWidgetIndex.value] || ''
})

const getData = async () => {
    const data = await getDecoratePages({ id: activeMenu.value })
    const currentIndex = menus.findIndex((item) => item.type == activeMenu.value)
    if (currentIndex !== -1) {
        menus[currentIndex].pageData = JSON.parse(data.data)
    }
}

const setData = async () => {
    const current = menus.find((item) => item.type == activeMenu.value)
    await setDecoratePages({
        ...current,
        data: JSON.stringify(current?.pageData || [])
    })
    getData()
}
watch(
    activeMenu,
    async () => {
        await getData()
    },
    {
        immediate: true
    }
)
</script>
<style lang="scss" scoped>
.decoration-pages {
    height: calc(100vh - var(--navbar-height) - 140px);
    @apply flex flex-col;
}
</style>
