<script lang="ts" setup>
import { noticeRead } from '~/api/app'
import { useAppStore } from '~/stores/app'

interface NoticePropsType {
    avatar: string
    content: string
    create_time: string
    id: number
    nickname: string
    read: 0 | 1
    robot: string
    robot_id: number
    balance: number // 审核成功获得的余额
    notice_type: 3 | 4 | 5 | 6 | 7 // 3-机器人，4-绘画，5-音乐，6-视频, 7-智能体
    records_name: string // 分享的作品名称
    verify_result: string // 审核失败原因
    verify_status: 1 | 2 // 1-审核成功，2-审核失败
    model: string // 模型
}

const emit = defineEmits(['read'])

const router = useRouter()
const appStore = useAppStore()
const props = defineProps<{
    data: NoticePropsType
    size?: string | undefined
}>()

const worksMap: Record<number, { name: string; bg: string; color: string, link: string }> = {
    4: { name: '绘画', bg: '#EAF3FF', color: '#4A92FF', link: '/draw/' },
    5: { name: '音乐', bg: '#FFF1E4', color: '#FF8F1F', link: '/music' },
    6: { name: '视频', bg: '#FFF0F0', color: '#FA5151', link: '/video' },
    7: { name: '智能体', bg: '#E7FFF7', color: '#00B578', link: '/application/layout/robot' }
    
}

const handleRead = async () => {
    if (props.data.notice_type === 3) {
        router.push({
            path: '/application/robot/setting',
            query: {
                id: props.data.robot_id,
                currentTab: 'dialogue',
                dialogue: true
            }
        })
    } else {
        if (props.data.notice_type === 4) {
            props.data.model === 'dalle3' ? router.push(worksMap[props.data.notice_type].link + 'dalle') :
            router.push(worksMap[props.data.notice_type].link + props.data.model)
        } else {
            router.push(worksMap[props.data.notice_type].link)
        }
    }
    if (props.data.read === 1) return
    await noticeRead({
        id: props.data.id
    })
    emit('read')
}
</script>

<template>
    <div
        class="flex items-stretch py-[20px] cursor-pointer"
        :class="[size === 'large' ? 'large' : 'default']"
        @click="handleRead"
    >
        <el-badge class="flex-none" :is-dot="!data.read" :offset="[0, 2]">
            <!--          notice_type： 3-机器人，4-绘画，5-音乐，6-视频        -->
            <template v-if="data.notice_type === 3">
                <ElImage
                    class="w-[40px] h-[40px] rounded-full"
                    :class="[
                        size === 'large'
                            ? 'w-[60px] h-[60px]'
                            : 'w-[40px] h-[40px]'
                    ]"
                    :src="data.avatar"
                ></ElImage>
            </template>
            <template v-else>
                <div
                    class="flex justify-center items-center rounded-full"
                    :class="[
                        size === 'large'
                            ? 'w-[60px] h-[60px] text-xl'
                            : 'w-[40px] h-[40px] text-sm'
                    ]"
                    :style="{
                        background: worksMap[data.notice_type].bg,
                        color: worksMap[data.notice_type].color
                    }"
                >
                    {{ worksMap[data.notice_type].name }}
                </div>
            </template>
        </el-badge>
        <div class="flex-1 ml-[15px]">
            <div class="flex justify-between items-center">
                <div
                    class="text-tx-primary text-base"
                    :class="[size === 'large' ? '' : 'line-clamp-1 w-[120px]']"
                >
                    {{ data.nickname }}
                </div>
                <div class="flex items-center text-tx-secondary text-xs">
                    <span class="ml-2">{{ data.create_time }}</span>
                </div>
            </div>

            <div
                class="mt-[12px] text-tx-primary text-base"
                :class="[size === 'large' ? '' : 'line-clamp-2']"
            >
                <!--          notice_type： 3-机器人，4-绘画，5-音乐，6-视频        -->
                <template v-if="data.notice_type === 3">
                    <span class="text-tx-secondary">在</span>
                    {{ data.robot }}
                    <span class="text-tx-secondary">反馈：</span>
                    {{ data.content }}
                </template>
                <template v-else>
                    <span class="text-tx-secondary">分享</span>
                    {{ data.records_name }}
                    <span class="text-tx-secondary">至广场</span>
                    <span
                        :class="[
                            data.verify_status === 1
                                ? 'text-success'
                                : 'text-error'
                        ]"
                    >
                        {{ data.verify_status === 1 ? '审核成功' : '审核失败' }}
                    </span>
                    <span v-if="data.verify_status == 2">，原因：{{ data.verify_result }}</span>
                    <span v-if="data.verify_status == 1 && data.balance > 0">
                      ， 获得 {{ data.balance }} {{ appStore.getTokenUnit }}
                    </span>
                </template>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.large {
    border-bottom: 1px solid #e0e3ea;

    :deep() {
        .el-badge__content.is-dot {
            width: 12px;
            height: 12px;
        }
    }
}

.default:hover {
    border-radius: 10px;
    @apply bg-primary-light-9;
}
</style>