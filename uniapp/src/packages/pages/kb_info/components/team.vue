<template>
    <view class="h-full">
        <view class="pt-[20rpx] bg-white h-full flex flex-col">
            <view class="flex justify-between items-center text-xl font-medium mb-[20rpx] px-[20rpx]">
                <text>团队成员(共{{ members.length || 0 }}人)</text>
                <view>
                    <u-button
                        v-if="currentUser.owned === 1"
                        type="primary"
                        size="medium"
                        @click="showAddUserModal"
                    >
                        +添加成员
                    </u-button>
                </view>
            </view>
            <scroll-view class="flex-1 min-h-0" scroll-y>
                <view class="py-3">
                    <view
                        v-for="item in members"
                        :key="item.id"
                        class="flex justify-between items-center mb-4 px-[20rpx]"
                        @click="toggleItemActionSheet(item)"
                    >
                        <view class="flex flex-1">
                            <view class="flex-none">
                                <u-image
                                    width="74"
                                    height="74"
                                    border-radius="50%"
                                    :src="item.avatar"
                                />
                            </view>
                            <view class="ml-3">
                                <view class="text-base">{{ item.nickname }}</view>
                                <u-tag
                                    v-if="item.owned === 1"
                                    type="primary"
                                    text="所有者"
                                    size="mini"
                                    style="--color-primary-light-3: transparent"
                                ></u-tag>
                                <u-tag
                                    v-if="item.owned === 2"
                                    type="primary"
                                    text="成员"
                                    size="mini"
                                    style="--color-primary-light-3: transparent"
                                ></u-tag>
                                <u-tag
                                    v-if="item.sn === currentUser.sn"
                                    type="primary"
                                    class="ml-2"
                                    text="我"
                                    size="mini"
                                    style="--color-primary-light-3: transparent"
                                ></u-tag>
                            </view>
                        </view>
                        <view class="flex-none">
                            <text v-if="item.owned === 1" class="mr-2 text-content">
                                全部
                            </text>
                            <text v-else class="mr-2 text-content">
                                {{ TEAM_PERMISSIONS[item.power] }}
                            </text>
                            <u-icon
                                v-if="currentUser.power === 1 && currentUser.sn !== item.sn && item.owned !== 1"
                                name="arrow-down"
                                size="22"
                                color="#666666"
                            ></u-icon>
                            <u-icon
                                v-if="currentUser.power >= 1 && currentUser.sn === item.sn && item.owned !== 1"
                                name="arrow-down"
                                size="22"
                                color="#666666"
                            ></u-icon>
                        </view>
                    </view>
                </view>
            </scroll-view>
        </view>

        <add-user
            ref="addUserModal"
            :id="KBId"
            @success="fetchTeamMembers"
        ></add-user>

        <u-popup
            v-model="isTransferModalVisible"
            closeable
            border-radius="20"
            mode="center"
            height="400"
        >
            <view
                class="flex flex-col min-h-0 h-full px-[30rpx]"
                style="width: 600rpx;"
            >
                <view class="py-[30rpx] font-medium text-lg">
                    成员处理方式
                </view>

                <view style="padding: 26rpx 0;">
                    <u-radio-group v-model="transferType">
                        <u-radio name="all" shape="square">
                            同步转移所有者的所有成员。
                        </u-radio>
                        <u-radio
                            name="kb"
                            shape="square"
                            class="flex !items-baseline mt-3"
                        >
                            <view style="width: 500rpx; line-height: 40rpx;">
                                不同步转移所有者的所有成员，只转移知识库。
                            </view>
                        </u-radio>
                    </u-radio-group>
                </view>

                <view class="flex justify-end">
                    <view>
                        <u-button
                            size="medium"
                            @click="isTransferModalVisible=false"
                        >
                            取消
                        </u-button>
                    </view>
                    <view class="ml-4">
                        <u-button
                            size="medium"
                            type="primary"
                            :loading="isTransferInProgress"
                            @click="transferOwnership"
                        >
                            确认
                        </u-button>
                    </view>
                </view>
            </view>
        </u-popup>

        <u-action-sheet
            :list="list"
            border-radius="24"
            :customItemStyles="{
                'font-size': '30rpx',
                'align-items': 'start',
                'padding-left': '30rpx'
            }"
            v-model="actionState.visible"
            @click="updatePermissions"
        ></u-action-sheet>
    </view>
</template>
<script lang="ts" setup>
import {computed, onMounted, shallowRef, ref, reactive} from 'vue'
import {deleteTeam, getTeamLists, putTeam, transferTeam} from '@/api/kb'
import {useUserStore} from '@/stores/user'
import {useKB} from '../useKb'

import {useRouter} from "uniapp-router-next";
const router = useRouter()
import AddUser from './team-add-user/index.vue'
const {KBId} = useKB()

interface TeamMember {
    id: number;
    nickname: string;
    sn: number;
    avatar: string;
    power: number;
    owned: number;
    create_time: string;
}

const TEAM_PERMISSIONS = {
    1: "可管理",
    2: "可编辑",
    3: "可查看",
};

const addUserModal = shallowRef<InstanceType<typeof AddUser>>()
const members = ref<TeamMember[]>([]);

const isTransferModalVisible = ref(false)
const transferType = ref<string>('all')
const isTransferInProgress = ref(false)
const transferTarget = reactive({sn: -1})

const list = ref<any[]>([])
const actionState = reactive({
    visible: false,
    memberId: 0,
})

const currentUser = computed(() =>
    members.value.find(member => member.sn === useUserStore().userInfo.sn) || {}
)
const isCurrentUserOwner = computed(() => currentUser.value.power === 1)
const isCurrentUserOwned = computed(() => currentUser.value.owned === 1)

const showAddUserModal = () => {
    addUserModal.value?.show()
}

const transferOwnership = async () => {
    isTransferInProgress.value = true
    await transferTeam({
        sn: transferTarget.sn,
        kb_id: KBId.value,
        type: transferType.value
    })
    if (transferType.value === 'kb') {
        setTimeout(() => {
            router.navigateBack()
        }, 1000)
    } else {
        isTransferInProgress.value = false
        isTransferModalVisible.value = false
        fetchTeamMembers()
    }
}

const buildActionSheetList = (isOwner: boolean, member: TeamMember) => {
    const actions = []

    if (isOwner) {
        const data = [
            {
                text: '可管理',
                fontSize: 30,
                color: '#333333',
                subText: '管理整个知识库数据和信息'
            },
            {
                text: '可编辑',
                fontSize: 30,
                color: '#333333',
                subText: '只能操作数据学习，增删改查自己的数据，不能修改他人'
            },
            {
                text: '可查看',
                fontSize: 30,
                color: '#333333',
                subText: '查看知识库所有数据'
            }
        ]
        actions.unshift(...data)
    }

    if (currentUser.value.owned === 1) {
        actions.push({
            text: '转移所有权',
            fontSize: 28,
            color: 'red',
        } as any)
    }

    if (currentUser.value.power === 1 && currentUser.value.sn !== member.sn) {
        actions.push({
            text: '移出团队',
            fontSize: 28,
            color: 'red',
        } as any)
    } else if (currentUser.value.power >= 1 && currentUser.value.sn === member.sn) {
        actions.push({
            text: '退出团队',
            fontSize: 28,
            color: 'red',
        } as any)
    }

    return actions
}

const toggleItemActionSheet = (member: TeamMember) => {
    list.value = buildActionSheetList(isCurrentUserOwner.value, member)
    if (member.owned === 1 || list.value.length === 0) {
        return
    }
    transferTarget.sn = member.sn
    actionState.visible = true
    actionState.memberId = member.id

}

const updatePermissions = async (index: number) => {
    if (list.value[index].text === '移出团队' || list.value[index].text === '退出团队') {
        await deleteTeam({id: actionState.memberId})
        if (list.value[index].text === '退出团队') {
            setTimeout(() => {
                router.navigateBack()
            }, 1000)
        }
    } else if (list.value[index].text === '转移所有权') {
        isTransferModalVisible.value = true
    } else {
        await putTeam({id: actionState.memberId, power: (index += 1)})
    }
    fetchTeamMembers()
}

const fetchTeamMembers = async () => {
    members.value = await getTeamLists({kb_id: KBId.value})
}

onMounted(() => {
    fetchTeamMembers()
})
</script>

<style lang="scss" scoped>

</style>
