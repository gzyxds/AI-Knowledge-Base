import { getKBDetail, fileImport, fileDataList } from '@/api/kb'
import { ref } from 'vue'

const KBInfo = ref()
const KBId = ref()
const KBName = ref('')

export const useKB = () => {
    const getKBInfo = async () => {
        KBInfo.value = await getKBDetail({ id: KBId.value })
        KBName.value = KBInfo.value.name
    }

    const setKBId = (id: number | string) => {
        KBId.value = Number(id)
    }

    const getFileList = async (params: any) => {
        await fileDataList(params)
    }

    const importData = async (params: any) => {
        await fileImport(params)
        getKBInfo()
    }

    const reset = () => {
        KBId.value = 0
        // KBInfo.value = getDefaultRobotInfo()
        KBName.value = ''
        // currentTab.value = ''
    }
    return {
        KBInfo,
        // currentTab,
        KBId,
        setKBId,
        getKBInfo,
        reset,
        KBName,
        importData
    }
}
