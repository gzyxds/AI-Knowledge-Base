<template>
    <div class="decoration-tabbar">
        <div class="flex-1 min-h-0 w-full">
            <div class="h-full">
                <ElScrollbar>
                    <div class="flex min-w-[900px] scroll-view-content">
                        <!-- <Menu class="!h-full flex-none" v-model="activeMenu" :menus="menus" /> -->
                        <div class="flex-1 pages-preview-box">
                            <div class="pages-preview mx-[30px]">
                                <el-scrollbar height="100%">
                                    <component
                                        :is="currentTab.component?.content"
                                        v-bind="currentTab.data"
                                    />
                                </el-scrollbar>
                            </div>
                        </div>
                        <el-scrollbar class="attr-setting flex-none bg-white">
                            <div class="p-5">
                                <component
                                    :is="currentTab.component?.attr"
                                    v-model="currentTab.data"
                                />
                            </div>
                        </el-scrollbar>
                    </div>
                </ElScrollbar>
            </div>
        </div>

        <footer-btns :fixed="true" v-perms="['decorate.page/save']">
            <el-button type="primary" @click="setData">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup name="decorationTabbar">
import { getDecoratePages, setDecoratePages } from '@/api/decoration'
import pcTab from './component/tabbar/pc'
import mobileTab from './component/tabbar/mobile'
enum ClientTypeEnum {
    MOBILE = 4,
    PC = 8
}
const route = useRoute()
const type = route.query.type
const activeMenu = ref(ClientTypeEnum.MOBILE)
if (type == 'pc') {
    activeMenu.value = ClientTypeEnum.PC
}
const menus: Record<
    string,
    {
        id: number
        name: string
        type: number
        data: any
        component: any
    }
> = reactive({
    [ClientTypeEnum.MOBILE]: {
        id: 4,
        type: 4,
        name: '移动端导航栏',
        data: {
            style: {
                default_color: '',
                selected_color: ''
            },
            list: []
        },
        component: mobileTab
    },
    [ClientTypeEnum.PC]: {
        id: 8,
        type: 8,
        name: 'PC端导航栏',
        data: {
            nav: [],
            menu: []
        },
        component: pcTab
    }
})
const currentTab = computed(() => {
    return menus[activeMenu.value]
})
const getData = async () => {
    const data = await getDecoratePages({
        id: currentTab.value.id
    })
    menus[String(data.id)].data = JSON.parse(data?.data)
}
const setData = async () => {
    const { id, type, name, data } = currentTab.value

    await setDecoratePages({
        id,
        type,
        name,
        data: JSON.stringify(data)
    })
    getData()
}

watch(
    activeMenu,
    () => {
        getData()
    },
    {
        immediate: true
    }
)
</script>
<style lang="scss" scoped>
$scroll-height: calc(100vh - var(--navbar-height) - 74px);
.decoration-tabbar {
    height: $scroll-height;
    width: 100%;
    @apply flex flex-col;
    .scroll-view-content {
        height: calc($scroll-height - 60px);
    }

    .pages-preview-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        height: 100%;

        .pages-preview {
            background-color: #ffffff;
            width: 360px;
            height: 615px;
            color: #333;
            position: relative;
        }
    }
    .nav-item {
        &:hover {
            .drag-move {
                display: block;
            }
        }
    }
    .attr-setting {
        width: 450px;
        height: 100%;
    }
}
</style>
