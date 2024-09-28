<template>
    <div class="h-full flex flex-col">
        <div class="px-[20px] py-[16px]">
            <div class="font-medium text-xl">我的形象</div>
        </div>
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div
                    class="px-[20px]"
                    v-infinite-scroll="load"
                    infinite-scroll-distance="50"
                >
                    <div v-if="userStore.isLogin">
                        <div class="flex flex-wrap items-stretch mx-[-10px]">
                            <Add @success="getLists">
                                <div class="w-[280px] h-[250px] mb-[20px]">
                                    <div
                                        class="mx-[10px] bg-body h-full rounded-[12px] p-[20px] overflow-hidden flex flex-col justify-center items-center cursor-pointer border-[rgba(67,111,246,0.2)] border-solid border"
                                    >
                                        <Icon name="el-icon-Plus" :size="24"/>
                                        <div class="mt-[10px]">新增形象</div>
                                    </div>
                                </div>
                            </Add>
                            <div
                                v-for="(item, index) in pageInfo.lists"
                                :key="index"
                                class="w-[280px] h-[250px] mb-[10px] card"
                            >
                                <div
                                    class="mx-[10px] bg-body h-full rounded-[12px] overflow-hidden flex flex-col border-[rgba(67,111,246,0.3)] border-solid border text-primary cursor-pointer relative"
                                    @click="goDetail(item)"
                                >
                                    <div
                                        class="flex text-center items-center justify-center text-white absolute inset-0 bg-[rgba(0,0,0,0.5)] z-[1000]"
                                        v-if="item.is_disable"
                                    >
                                        <div>
                                            该形象涉权违规，已禁用
                                            <br/>
                                            请重新创建形象
                                        </div>
                                    </div>
                                    <ElImage
                                        :src="item.image"
                                        class="w-full h-[190px] !flex"
                                        fit="cover"
                                    />
                                    <div class="card-info">
                                        <ElImage
                                            class="w-[40px] h-[40px] rounded-[50%] overflow-hidden border border-solid border-white flex-none"
                                            fit="cover"
                                            :src="item.avatar"
                                        />
                                        <div
                                            class="flex-1 text-white ml-[10px] line-clamp-2 text-bold"
                                            style="word-break: break-all"
                                        >
                                            {{ item.name }}
                                        </div>
                                        <div class="bg-white rounded-[50%] flex p-[4px] ml-[5px]">
                                            <Icon name="el-icon-ArrowRight"/>
                                        </div>
                                    </div>

                                    <div
                                        class="delete-icon rounded-[50%] flex p-[6px] ml-[5px] absolute top-[10px] right-[14px] cursor-pointer text-tx-primary z-[10000]"
                                        @click.stop="handelDel(item.id)"
                                    >
                                        <Icon name="el-icon-Delete"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="!userStore.isLogin">
                        <tologin/>
                    </div>
                </div>
            </ElScrollbar>
        </div>
    </div>
</template>

<script setup lang="ts">
import {useUserStore} from '@/stores/user'
import {useAppStore} from '~/stores/app'
import tologin from '@/layouts/components/account/tologin.vue'
import {delDigital, getDigitalList} from '@/api/digital'
import Add from '../digital/_components/add.vue'

const userStore = useUserStore()
const router = useRouter()
const appStore = useAppStore()
const goDetail = async (item?: any) => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (item.is_disable) {
        return
    }
    router.push({
        path: '/application/digital/edit',
        query: {
            id: item.id
        }
    })
}

const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 15,
    lists: [] as any[]
})

const getLists = async () => {
    const data = await getDigitalList({
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

await useAsyncData(() => getLists(), {
    lazy: true
})

const handelDel = async (id: string | number) => {
    await feedback.confirm('确定删除？')
    await delDigital({id})
    getLists()
}
definePageMeta({
    parentPath: '/application/layout/robot',
    title: '虚拟形象'
})
</script>

<style lang="scss" scoped>
.card-info {
    background: linear-gradient(
            90deg,
            rgba(112, 195, 236, 0.95) 0%,
            rgba(66, 109, 247, 0.95) 100%
    );
    transform: translateY(-40%);
    margin: 0 15px;
    border-radius: 100px;
    padding: 5px 12px;
    display: flex;
    align-items: center;
}

.card {
    .delete-icon {
        opacity: 0;
        @apply bg-body;
    }

    &:hover {
        .delete-icon {
            opacity: 1;
        }
    }
}
</style>
