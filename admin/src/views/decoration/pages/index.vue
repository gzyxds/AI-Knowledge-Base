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
<script lang="ts" setup name="decorationPages">
import Menu from '../component/pages/menu.vue'
import Preview from '../component/pages/preview.vue'
import AttrSetting from '../component/pages/attr-setting.vue'
import widgets from '../component/widgets'
import { getDecoratePages, setDecoratePages } from '@/api/decoration'
import { getNonDuplicateID } from '@/utils/util'
enum pagesTypeEnum {
    USER = 2,
    SERVICE = 3,
    HOME = 7,
    INVITE = 9,
    TASK = 10,
    POSTER = 12
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
        id: pagesTypeEnum.HOME,
        type: pagesTypeEnum.HOME,
        name: '首页装修',
        pageData: []
        // pageData: generatePageData([
        //     'index-title',
        //     'index-banner',
        //     'index-ad',
        //     'index-menu',
        //     'index-hot'
        // ])
    },
    {
        id: pagesTypeEnum.TASK,
        type: pagesTypeEnum.TASK,
        name: '任务奖励',
        pageData: generatePageData(['task-center'])
    },
    {
        id: pagesTypeEnum.USER,
        type: pagesTypeEnum.USER,
        name: '个人中心',
        pageData: generatePageData([
            'user-info',
            'open-vip',
            'user-balance',
            'my-service',
            'user-banner',
            'user-bottom'
        ])
    },
    {
        id: pagesTypeEnum.INVITE,
        type: pagesTypeEnum.INVITE,
        name: '邀请海报',
        pageData: generatePageData(['invite-poster', 'invite-rule'])
    },

    {
        id: pagesTypeEnum.POSTER,
        type: pagesTypeEnum.POSTER,
        name: '对话海报',
        pageData: []
    },
    {
        id: pagesTypeEnum.SERVICE,
        type: pagesTypeEnum.SERVICE,
        name: '客服设置',
        pageData: generatePageData(['customer-service'])
    }
])

const activeMenu = ref(pagesTypeEnum.HOME)
const selectWidgetIndex = ref(-1)
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
        selectWidgetIndex.value = -1
        await getData()
        selectWidgetIndex.value = getPageData.value.findIndex((item) => !item.disabled)
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
