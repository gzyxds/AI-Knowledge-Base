<template>
    <div class="flex flex-col min-h-0 h-full p-5" @click.stop="hidePopovers">
        <div class="text-xl font-bold">我的团队</div>

        <div class="flex justify-between items-center py-2">
            <div>团队成员(共{{ members.length }}人)</div>
            <el-button
                v-if="currentUser.owned === 1"
                type="primary"
                plain
                @click="showAddUserModal"
            >
                + 添加成员
            </el-button>
        </div>

        <el-table
            class="mt-4 cursor-pointer flex-1 min-h-0"
            :data="members"
            size="large"
            height="100%"
            row-class-name="h-[70px]"
        >
            <el-table-column label="成员信息" prop="name" min-width="160">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-avatar
                            :src="row.avatar"
                            size="26"
                            class="flex-none"
                        />
                        <OverflowTooltip
                            class="ml-2"
                            :content="row.nickname"
                            :teleported="true"
                            effect="light"
                        />
                        <el-tag
                            v-if="row.sn === currentUser.sn"
                            type="primary"
                            size="small"
                            class="ml-2"
                        >
                            我
                        </el-tag>
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="成员角色" prop="role" min-width="160">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <div v-if="row.owned === 1">所有者</div>
                        <el-popover
                            v-else-if="isCurrentUserPower && currentUser.sn !== row.sn"
                            placement="bottom"
                            :width="180"
                            trigger="click"
                            :show-arrow="false"
                            transition="custom-popover"
                            :visible="isRemovePopoverVisible(row)"
                        >
                            <div
                                class="flex items-center leading-10 cursor-pointer hover:bg-page px-2 rounded text-error"
                                @click="removeMember(row)"
                            >
                                移出团队
                            </div>
                            <template #reference>
                                <div
                                    class="flex items-center cursor-pointer"
                                    @click.stop="toggleRemovePopover(row)"
                                >
                                    <span class="text-base mr-1">成员</span>
                                    <Icon name="el-icon-ArrowDown" />
                                </div>
                            </template>
                        </el-popover>
                        <el-popover
                            v-else-if="currentUser.power >= 1 && currentUser.sn === row.sn"
                            placement="bottom"
                            :width="180"
                            trigger="click"
                            :show-arrow="false"
                            transition="custom-popover"
                            :visible="isRemovePopoverVisible(row)"
                        >
                            <div
                              class="flex items-center leading-10 cursor-pointer hover:bg-page px-2 rounded text-error"
                              @click="removeMember(row)"
                            >
                                退出团队
                            </div>
                            <template #reference>
                                <div
                                  class="flex items-center cursor-pointer"
                                  @click.stop="toggleRemovePopover(row)"
                                >
                                    <span class="text-base mr-1">成员</span>
                                    <Icon name="el-icon-ArrowDown" />
                                </div>
                            </template>
                        </el-popover>
                        <div v-else class="text-base">成员</div>
                    </div>
                </template>
            </el-table-column>

            <el-table-column
                label="默认权限"
                prop="permissions"
                min-width="160"
            >
                <template #default="{ row }">
                    <div class="flex items-center">
                        <div v-if="row.owned === 1">全部</div>
                        <el-popover
                            v-else-if="isCurrentUserPower"
                            placement="bottom-end"
                            :width="390"
                            trigger="click"
                            :show-arrow="false"
                            transition="custom-popover"
                            :visible="isPermissionPopoverVisible(row)"
                        >
                            <div class="cursor-pointer">
                                <PermissionOption
                                    label="可编辑"
                                    description="只能操作数据学习，增删改查自己的数据，不能修改他人"
                                    :value="2"
                                    :model-value="selectedPermission"
                                    @change="updatePermissions(row, 2)"
                                />
                                <PermissionOption
                                    label="可查看"
                                    description="查看知识库所有数据"
                                    :value="3"
                                    :model-value="selectedPermission"
                                    @change="updatePermissions(row, 3)"
                                />
                                <PermissionOption
                                    v-if="isCurrentUserPower"
                                    label="可管理"
                                    description="管理整个知识库数据和信息"
                                    :value="1"
                                    :model-value="row.power"
                                    @change="updatePermissions(row, 1)"
                                />
                            </div>
                            <template #reference>
                                <div
                                    class="flex items-center cursor-pointer"
                                    @click.stop="showPermissionPopover(row)"
                                >
                                    <span class="text-base mr-1">{{
                                        permissionLabel(row.power)
                                    }}</span>
                                    <Icon name="el-icon-ArrowDown" />
                                </div>
                            </template>
                        </el-popover>
                        <div v-else class="text-base">
                            {{ permissionLabel(row.power) }}
                        </div>
                    </div>
                </template>
            </el-table-column>

            <el-table-column
                label="加入时间"
                prop="create_time"
                min-width="150"
            />
            <el-table-column
                v-if="isCurrentUserOwned"
                label="操作"
                min-width="200"
                fixed="right"
            >
                <template #default="{ row }">
                    <el-button
                        v-if="canTransferOwnership(row)"
                        type="primary"
                        link
                        @click="showTransferModal(row)"
                    >
                        转移所有权
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <team-add-user
            ref="addUserModal"
            :id="teamId"
            @success="fetchTeamMembers"
        />

        <el-dialog
            v-model="isTransferModalVisible"
            width="450px"
            class="!rounded-[12px]"
            center
            draggable
            destroy-on-close
            close-on-click-modal="false"
        >
            <template #header>
                <div class="w-full text-left">
                    <div class="text-lg font-medium">成员处理方式</div>
                </div>
            </template>
            <div>
                <el-checkbox
                    v-model="transferType"
                    true-value="all"
                    label="同步转移所有者的所有成员。"
                    size="large"
                />
                <el-checkbox
                    v-model="transferType"
                    true-value="kb"
                    label="不同步转移所有者的所有成员，只转移知识库。"
                    size="large"
                />
            </div>
            <template #footer>
                <div class="flex justify-end">
                    <el-button @click="isTransferModalVisible = false"
                        >取消</el-button
                    >
                    <el-button
                        type="primary"
                        :loading="isTransferInProgress"
                        @click="transferOwnership"
                        >确认
                    </el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, shallowRef } from 'vue'
import { useUserStore } from '~/stores/user'
import {
    getTeamLists,
    putTeam,
    deleteTeam,
    transferTeam
} from '~/api/knowledge'
import TeamAddUser from './team-data-com/add-user.vue'
import PermissionOption from './team-data-com/permission-option.vue'
import { ElButton, ElCheckbox, ElDialog, ElDivider } from 'element-plus'

interface TeamMember {
    id: number
    nickname: string
    sn: number
    avatar: string
    power: number
    owned: boolean
    create_time: string
}

const TEAM_PERMISSIONS = {
    1: '可管理',
    2: '可编辑',
    3: '可查看'
}

const props = defineProps({ id: { type: Number, default: 0 } })

const router = useRouter()
const teamId = ref(props.id)
const members = ref<TeamMember[]>([])
const currentUser = computed(
    () =>
        members.value.find(
            (member) => member.sn === useUserStore().userInfo.sn
        ) || {}
)
const selectedPermission = ref<number | null>(null)

const isTransferModalVisible = ref(false)
const transferType = ref<string>('all')
const isTransferInProgress = ref(false)
const transferTarget = reactive({ sn: -1 })

const addUserModal = shallowRef<InstanceType<typeof TeamAddUser>>()

const fetchTeamMembers = async () => {
    members.value = await getTeamLists({ kb_id: teamId.value })
}

const hidePopovers = () => {
    removeState.visible = false
    powerState.visible = false
}

const showAddUserModal = () => {
    addUserModal.value?.show()
}

const removeState = reactive({ visible: false, memberId: '' })

const toggleRemovePopover = (member: TeamMember) => {
    removeState.visible = !removeState.visible
    removeState.memberId = member.id.toString()
}

const isRemovePopoverVisible = (member: TeamMember) =>
    removeState.visible && removeState.memberId === member.id.toString()

const removeMember = async (member: TeamMember) => {
    try {
        await feedback.confirm('确定删除该成员？', '删除成员')
        await deleteTeam({ id: member.id })
        if (currentUser.value.sn === member.sn) {
            setTimeout(() => {
                router.back()
            }, 1000)
        }
        fetchTeamMembers()
    } finally {
        removeState.visible = false
    }
}

const powerState = reactive({ visible: false, memberId: '' })

const showPermissionPopover = (member: TeamMember) => {
    powerState.visible = true
    powerState.memberId = member.id.toString()
    selectedPermission.value = member.power
}

const isPermissionPopoverVisible = (member: TeamMember) =>
    powerState.visible && powerState.memberId === member.id.toString()

const updatePermissions = async (member: TeamMember, power: number) => {
    selectedPermission.value = power
    await putTeam({ id: member.id, power })
    powerState.visible = false
    fetchTeamMembers()
}

const showTransferModal = (member: TeamMember) => {
    transferTarget.sn = member.sn
    isTransferModalVisible.value = true
}

const transferOwnership = async () => {
    isTransferInProgress.value = true
    await transferTeam({
        sn: transferTarget.sn,
        kb_id: teamId.value,
        type: transferType.value
    })
    isTransferInProgress.value = false
    if (transferType.value === 'kb') {
        setTimeout(() => {
            router.back()
        }, 1000)
    } else {
        isTransferModalVisible.value = false
        fetchTeamMembers()
    }
}

const permissionLabel = (power: number) => TEAM_PERMISSIONS[power] || '未知权限'
const isCurrentUserPower = computed(() => currentUser.value.power === 1)
const isCurrentUserOwned = computed(() => currentUser.value.owned === 1)

const canTransferOwnership = (member: TeamMember) =>
    member.sn !== currentUser.value.sn && isCurrentUserPower.value

onMounted(() => {
    fetchTeamMembers()
})
</script>

<style scoped>
.custom-popover {
    animation: slide-in-bottom 0.3s ease-out;
}

@keyframes slide-in-bottom {
    from {
        transform: translateY(-10px);
    }
    to {
        transform: translateY(0);
    }
}
</style>
