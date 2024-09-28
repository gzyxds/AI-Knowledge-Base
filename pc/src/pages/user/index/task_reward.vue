<template>
    <div class="task-reward flex flex-col min-h-0 h-full bg-body rounded-[12px]">
        <div
            v-if="data.length"
            class="flex flex-wrap cursor-pointer pr-[20px]"
        >
            <Waterfall
                ref="waterFull"
                :delay="100"
                :list="data"
                :width="364"
                :gutter="20"
                :animationDelay="0"
                :animationDuration="0"
                backgroundColor="none"
                animationPrefix="none"
                animated="none"
                animationEffect="none"
                :breakpoints="breakpoints"
            >
                <template #item="{ item }">
                    <div class="p-[30px] bg-page rounded-[12px]">
                        <div class="flex justify-between">
                            <el-image
                                :src="item.image"
                                class="w-[70px] h-[70px] rounded-[12px]"
                            >
                            </el-image>

                            <el-button
                                style="--el-button-bg-color: #4a92ff"
                                class="!border-none"
                                type="primary"
                                :disabled="item.data.num >= item.data.day_num"
                                @click.stop="handleTask(item.type)"
                            >
                                {{ typeMap[item.type].btn_text }}
                            </el-button>
                        </div>
                        <div>
                            <div class="text-xl font-medium mt-[20px]">
                                {{ item.customName || item.name }}
                                (<span v-html="typeMap[item.type].num"></span>)
                            </div>
                            <div class="text-base text-tx-secondary mt-[5px]">
                                <div v-html="typeMap[item.type].desc"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </Waterfall>
        </div>
        <!--        空状态时        -->
        <div v-else class="flex items-center justify-center h-full">
            <el-empty
                :image="EmptyLayer"
                :image-size="250"
                description="暂无任务奖励"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'

import { getShareTaskList, getShareId, signClick } from '~/api/task_reward'
import { useCopy } from '~/composables/useCopy'
import EmptyLayer from 'assets/image/empty_layer.png'

interface TaskRewardItem {
    customName: string
    data: {
        day_num: number
        is_open: 0 | 1
        num: number
        one_award: number
    }
    title: string
    image: string
    name: string
    show: 0 | 1
    type: number
}

const { copy } = useCopy()
const router = useRouter()
const userStore = useUserStore()
const { getTokenUnit, config } = useAppStore()
const typeMap: Record<
    number,
    {
        num: string
        btn_text: string
        desc: string
        payload: string
    }
> = {
    1: {
        num: '0/1',
        btn_text: '立即签到',
        desc: '',
        payload: '每天签到，可获得'
    },
    2: {
        num: '0/10',
        btn_text: '复制链接',
        desc: '',
        payload: '邀请1人，可获得'
    },
    3: {
        num: '0/3',
        btn_text: '复制链接',
        desc: '',
        payload: '分享1次，可获得'
    },
    4: {
        num: '0/4',
        btn_text: '立即分享',
        desc: '',
        payload: '分享1次，可获得'
    },
    5: {
        num: '0/3',
        btn_text: '立即分享',
        desc: '',
        payload: '分享1次，可获得'
    },
    6: {
        num: '0/3',
        btn_text: '立即分享',
        desc: '',
        payload: '分享1次，可获得'
    },
    7: {
        num: '0/3',
        btn_text: '立即分享',
        desc: '',
        payload: '分享1次，可获得'
    }
}
const breakpoints: Record<number, { rowPerView: number }> = {
    4000: { rowPerView: 6 },
    2000: { rowPerView: 5 },
    1800: { rowPerView: 4 },
    1600: { rowPerView: 4 },
    1440: { rowPerView: 4 },
    1360: { rowPerView: 3 },
    1280: { rowPerView: 3 },
    1024: { rowPerView: 3 }
}

const { data: data, refresh: getData } = await useAsyncData(
    () => getShareTaskList(),
    {
        default() {
            return []
        },
        lazy: true,
        transform: (data) => {
            const res: TaskRewardItem[] = JSON.parse(data.data)
            return res.filter((item: TaskRewardItem) => {
                if (item.type === 2 || item.type === 3) {
                    typeMap[item.type].btn_text = '复制链接'
                } else if (item.data.num >= item.data.day_num) {
                    typeMap[item.type].btn_text =
                        item.type === 1 ? '已签到' : '已分享'
                }

                typeMap[
                    item.type
                ].num = `${item.data?.num}/${item.data?.day_num}`
                typeMap[item.type].desc = `${typeMap[item.type].payload}${
                    item.data?.one_award
                }${getTokenUnit}`

                return item.data.is_open === 1
            })
        }
    }
)

const handleTask = async (type: number) => {
    switch (type) {
        case 1:
            await signClick()
            userStore.getUser()
            getData()
            break
        case 2:
            getShareId().then(({ share_id }) => {
                copy(`${config.current_domain}/?share_id=${share_id}`)
            })
            break
        case 3:
            getShareId().then(({ share_id }) => {
                copy(`${config.current_domain}/?share_id=${share_id}`)
            })
            break
        case 4:
            await router.push('/draw/sd')
            break
        case 5:
            await router.push('/video')
            break
        case 6:
            await router.push('/music')
            break
        case 7:
            await router.push('/application/layout/robot')
            break
    }
}
</script>