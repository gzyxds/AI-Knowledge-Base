import { getRobotDetail } from '@/api/robot'
import { onMounted, onUnmounted, reactive, ref } from 'vue'
const getDefaultRobotInfo = () => ({
    kb_ids: [],
    icons: '',
    image: '',
    name: '',
    intro: '',
    model_id: '',
    model_sub_id: '',
    roles_prompt: '',
    search_similarity: 0.8,
    search_limits: 3,
    search_empty_type: 1,
    search_empty_text: '',
    welcome_introducer: '',
    is_show_context: 1,
    is_show_quote: 1,
    is_enable: 1,
    menus: []
})
const robotId = ref<number | string>()
const robotInfo = ref(getDefaultRobotInfo())
const robotName = ref('')
const currentTab = ref('')
export const useRobot = () => {
    const getRobotInfo = async () => {
        robotInfo.value = await getRobotDetail({ id: robotId.value })
        robotName.value = robotInfo.value.name
    }

    const setRobotId = (id: number | string) => {
        robotId.value = id
    }

    const reset = () => {
        robotId.value = 0
        robotInfo.value = getDefaultRobotInfo()
        robotName.value = ''
        currentTab.value = ''
    }
    return {
        robotInfo,
        currentTab,
        setRobotId,
        getRobotInfo,
        reset,
        robotName
    }
}
