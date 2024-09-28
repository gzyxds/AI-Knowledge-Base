<template>
    <div class="h-full flex flex-col">
        <div class="px-[20px] py-[16px] mt-[-8px]">
            <!-- <div class="font-medium text-xl">全部知识库</div> -->
            <el-tabs
                v-model="queryParams.type"
                class="demo-tabs"
                @tab-change="handleClick"
            >
                <el-tab-pane label="全部知识库" name="0"></el-tab-pane>
                <el-tab-pane label="我的知识库" name="1"></el-tab-pane>
                <el-tab-pane label="共享给我" name="2"></el-tab-pane>
            </el-tabs>
        </div>

        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div
                    v-if="userStore.isLogin"
                    class="flex flex-wrap px-[20px]"
                    v-infinite-scroll="load"
                    infinite-scroll-distance="50"
                >
                    <div
                        class="sm:w-[200px] w-full bg-body rounded-[12px] overflow-hidden cursor-pointer mr-[20px] flex-none mb-[20px] flex flex-col items-center justify-center min-h-[150px]"
                        @click="addDatabase"
                    >
                        <Icon name="el-icon-Plus" :size="24" />
                        <div class="mt-[10px]">新增知识库</div>
                    </div>
                    <div
                        v-for="(item, index) in pageInfo.lists"
                        :key="index"
                        class="kb-item sm:w-[200px] w-full bg-body rounded-[12px] overflow-hidden cursor-pointer mr-[20px] flex-none mb-[20px]"
                        @click="toDetail(item.id, 'dataStudy')"
                    >
                        <div class="flex relative">
                            <el-image
                                :src="item.image"
                                class="w-full h-[160px]"
                                fit="cover"
                            />
                            <div
                                class="bg-[rgba(0,0,0,0.4)] text-white absolute px-1 right-[10px] top-[10px] rounded flex items-center"
                                @click.stop="toDetail(item.id, 'teamData')"
                            >
                                <icon name="el-icon-User" />
                                <span class="ml-1 text-sm">
                                    {{ item.team_people }}</span
                                >
                            </div>
                            <div
                                v-if="item.is_super"
                                class="kb-btns absolute bottom-0 left-0 w-full flex bg-[rgba(0,0,0,0.4)] py-[6px] text-white"
                            >
                                <div
                                    class="flex flex-1 items-center justify-center"
                                    @click.stop="editDatabase(item.id)"
                                >
                                    <icon name="el-icon-Edit" />
                                    <span class="ml-1"> 编辑 </span>
                                </div>
                                <div
                                    class="flex flex-1 items-center justify-center"
                                    @click.stop="
                                        delDatabase(item.id, item.name)
                                    "
                                >
                                    <icon name="el-icon-Delete" />
                                    <span class="ml-1"> 删除 </span>
                                </div>
                            </div>
                        </div>

                        <div class="px-[15px] py-[12px]">
                            <div class="text-[18px] truncate">
                                {{ item.name }}
                            </div>
                            <div class="flex items-center mt-[10px]">
                                <!-- <div class="bg-[#F5F6F8] text-[#4A5164] px-[8px] rounded">
                                    {{ item.type == 1 ? '文档型' : '问答型' }}
                                  </div> -->
                                <div class="text-info flex-1 min-w-0 truncate">
                                    {{ item.intro || '这个知识库还没介绍呢～' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!userStore.isLogin">
                    <tologin />
                </div>
            </ElScrollbar>
        </div>
    </div>
    <add-pop
        v-if="showPop"
        ref="popRef"
        @success="
            () => {
                showPop = false
                getLists()
            }
        "
    />
</template>
<script setup lang="ts">
import addPop from '../kb/_components/addPop.vue'
import tologin from '@/layouts/components/account/tologin.vue'
import { knowKnowledgeDel, knowKnowledgeList } from '@/api/my_database'
import { useUserStore } from '@/stores/user'
import { useRechargeStore } from '@/stores/recharge'
import { useAppStore } from '@/stores/app'

const router = useRouter()
const userStore = useUserStore()
const rechargeStore = useRechargeStore()
const appStore = useAppStore()

//请求列表参数
const queryParams = ref({
    type: '0'
})

//分享给用户弹窗数据载体
const shareUserState = reactive({
    show: false,
    num: 0,
    kbId: 0,
    isOwner: false
})

//新增/编辑的弹窗载体
const showPop = ref(false)
const popRef = shallowRef()

// 页面数据
const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 15,
    lists: [] as any[]
})

//tabs切换
const handleClick = async () => {
    pageInfo.pageNo = 1
    await nextTick()
    await getLists()
}

const getLists = async () => {
    const data = await knowKnowledgeList({
        ...queryParams.value,
        page_no: pageInfo.pageNo,
        page_size: pageInfo.pageSize
    })
    pageInfo.count = data.count
    if (pageInfo.pageNo === 1) {
        pageInfo.lists = []
    }
    pageInfo.lists.push(...data.lists)
}

const load = () => {
    if (!userStore.isLogin) return
    if (pageInfo.count >= pageInfo.pageNo * pageInfo.pageSize) {
        pageInfo.pageNo++
        getLists()
    }
}

const showShareUser = (item: any) => {
    shareUserState.show = true
    shareUserState.num = item.share_user
    shareUserState.kbId = item.id
    shareUserState.isOwner = item.is_super
}

//打开新增弹框
const addDatabase = async () => {
    if (userStore.userInfo.kb_num <= 0) {
        if (!appStore.getIsShowRecharge) {
            feedback.msgError('知识库数量已用完。请联系客服增加')
        } else {
            await feedback.confirm('知识库数量已用完，请前往充值')
            rechargeStore.toggleShow(true)
        }
        return
    }
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    showPop.value = true
    await nextTick()
    popRef.value.open()
}

//打开编辑弹框
const editDatabase = async (id: number) => {
    showPop.value = true
    await nextTick()
    popRef.value.open({ id })
}

//删除数据库
const delDatabase = async (id: number, name: string) => {
    await feedback.confirm(`确认删除 ${name} 吗？`)
    await knowKnowledgeDel({ id })
    getLists()
}

//跳转至详情页
const toDetail = (id: number, type: string) => {
    router.push(`/application/kb/detail?id=${id}&type=${type}`)
}

onMounted(() => {
    getLists()
})

definePageMeta({
    parentPath: '/application/layout/robot',
    title: '我的知识库'
})
</script>
<style scoped lang="scss">
:deep(.el-tabs__nav-wrap::after) {
    background-color: transparent !important;
}

.btnColor {
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
}

:deep(.el-tabs) {
    .el-tabs__header {
        margin-bottom: 0;
    }

    .el-tabs__item {
        font-size: 16px;
    }

    .el-tabs__nav-wrap::after {
        height: 0;
    }
}

.kb-item {
    .kb-btns {
        display: none;
    }

    &:hover {
        .kb-btns {
            display: flex;
        }
    }
}
</style>