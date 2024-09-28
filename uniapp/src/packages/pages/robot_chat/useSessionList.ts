import {
    postRobotCate,
    clearRobotCate,
    delRobotCate,
    putRobotCate,
    getRobotCateLists
} from '@/api/robot'
import { useLockFn } from '@/hooks/useLockFn'
import { computed, ref } from 'vue'

export function useSessionList(robotId: string | number) {
    const sessionActive = ref(0)
    const sessionLists = ref<any[]>([])
    const getSessionLists = async () => {
        try {
            const data = await getRobotCateLists({
                robot_id: robotId
            })
            sessionLists.value = data || []
            if (sessionActive.value === 0) {
                initSessionActive()
            }
            return sessionLists.value
        } catch (error) {
            sessionLists.value = []
            initSessionActive()
        }
    }

    const { lockFn: sessionAdd } = useLockFn(async () => {
        await postRobotCate({
            robot_id: robotId
        })
        await getSessionLists()
        initSessionActive()
    })

    const sessionDelete = async (id: number) => {
        const res = await uni.showModal({
            title: '确定删除该会话？'
        })
        if (res.confirm) {
            await delRobotCate({ id, robot_id: robotId })
            await getSessionLists()
            initSessionActive()
        }
    }

    const sessionClear = async () => {
        const res = await uni.showModal({
            title: '确定清除所有会话？'
        })
        if (res.confirm) {
            await clearRobotCate({
                robot_id: robotId
            })
            await getSessionLists()
            initSessionActive()
        }
    }

    const sessionEdit = async (data: { id: number; name: string }) => {
        await putRobotCate({ ...data, robot_id: robotId })
        getSessionLists()
    }

    const initSessionActive = () => {
        sessionActive.value = sessionLists.value[0]?.id || 0
    }

    const currentSession = computed(() => {
        return (
            sessionLists.value.find(
                (session) => session.id === sessionActive.value
            )?.name || ''
        )
    })

    return {
        getSessionLists,
        sessionActive,
        sessionLists,
        sessionAdd,
        sessionEdit,
        sessionDelete,
        sessionClear,
        currentSession
    }
}
