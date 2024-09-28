<template>
    <div class="p-[20px] flex bg-body rounded-[12px] flex-col h-full">
        <div
            class="grid grid-cols-2 md:grid-cols-2 gap-4 bg-page py-[20px] rounded-lg flex-none"
        >
            <div class="flex flex-col items-center justify-center">
                <div class="font-medium text-[25px] text-[#0256FF]">
                    {{ userInfo.balance }}
                </div>
                <div class="mt-2">{{ appStore.getTokenUnit }}数量</div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="font-medium text-[25px] text-[#0256FF]">
                    {{ userInfo.robot_num }}
                </div>
                <div class="mt-2">智能体</div>
            </div>
            <!-- <div class="flex flex-col items-center justify-center">
                <div class=" font-medium text-[20px]">10000.4343</div>
                <div class="mt-2">知识库</div>
            </div> -->
            <!-- <div class="flex flex-col items-center justify-center">
                <div class=" font-medium text-[20px]">{{ userInfo.video_num }}</div>
                <div class="mt-2">形象时长</div>
            </div> -->
        </div>
        <div class="flex mt-4 flex-none">
            <div
                class="p-[8px] flex justify-around bg-page rounded-[10px] font-medium"
            >
                <div
                    :class="{ isSelect: params.type == item.type }"
                    class="px-[15px] md:px-[30px] py-[10px] cursor-pointer"
                    v-for="(item, index) in typeList"
                    :key="index"
                    @click="toSelect(item.type)"
                >
                    <span>{{ item.name }}</span>
                </div>
            </div>
        </div>
        <div class="mt-4 flex-1 min-h-0 flex flex-col">
            <div class="flex-1 min-h-0">
                <el-table :data="pager.lists" height="100%">
                    <el-table-column
                        label="订单编号"
                        prop="sn"
                        min-width="150"
                    ></el-table-column>
                    <el-table-column
                        label="变动类型"
                        prop="change_type"
                        min-width="150"
                    ></el-table-column>
                    <el-table-column
                        label="智能体/应用名"
                        prop="robot_name"
                        min-width="150"
                    >
                        <template #default="{ row }">
                            <div>{{ row.robot_name || '-' }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column
                        label="操作时间"
                        prop="create_time"
                        min-width="150"
                    ></el-table-column>
                    <el-table-column
                        :label="`变动${watchType}`"
                        prop="change_amount"
                        min-width="100"
                    >
                        <template #default="{ row }">
                            <div :class="{ 'text-danger': row.action == 2 }">
                                <span>{{ row.action == 1 ? '+' : '-' }}</span>
                                <span>{{ row.change_amount }}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" min-width="80">
                        <template #default="{ row }">
                            <el-button
                                @click="showDetail(row.id)"
                                link
                                type="primary"
                            >
                                详情
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </div>

        <detailPop
            :type="watchType"
            v-if="popShow"
            ref="popRef"
            @close="popShow = false"
        ></detailPop>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user'
import detailPop from './_components/recordDetailPop.vue'
import { useAppStore } from '@/stores/app'
import { accountList } from '@/api/account'

const { userInfo } = useUserStore()
const appStore = useAppStore()

const params = ref({
    type: 1
})
//监控type
const watchType = computed(() => {
    return '数量'
})

const popShow = ref(false)
const popRef = shallowRef()

const typeList = computed(() => [
    {
        name: `${appStore.getTokenUnit}明细`,
        type: 1
    },
    {
        name: '智能体明细',
        type: 2
    }
    // {
    //     name: '知识库明细',
    //     type: 'knowledgeBase'
    // },
    // {
    //     name: '形象明细',
    //     type: 3
    // }
])

//选择
const toSelect = async (type: number) => {
    params.value.type = type
    await nextTick()
    getLists()
}

const { pager, getLists } = usePaging({
    fetchFun: accountList,
    params: params.value
})

//详情弹框
const showDetail = async (id: number) => {
    popShow.value = true
    await nextTick()
    popRef.value.open(id)
}

onMounted(() => {
    getLists()
})
</script>

<style scoped lang="scss">
.isSelect {
    color: white;
    border-radius: 8px;
    @apply bg-primary;
}
</style>
