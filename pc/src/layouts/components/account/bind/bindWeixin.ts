import { Ref } from 'nuxt/dist/app/compat/capi'
import usePolling from '~/composables/usePolling'
import { checkTicket, getWxCode } from '~/api/account'

export const useGetCode = () => {
    const state = useState<{
        pending: boolean
        data: any
    }>(() => ({
        pending: true,
        data: {}
    }))

    const getData = async () => {
        try {
            state.value.pending = true
            const data = await getWxCode({
              channel:'bind'
            })
            state.value.data = data
            state.value.pending = false
        } catch (error: any) {
            state.value.pending = false
            return Promise.reject(error)
        }
    }

    return {
        state,
        getData
    }
}

export enum WxBindStatus {
    CODE_ERROR = -1,
    INVALID = 0,
    NORMAL = 1,
    SCANNED_CODE = 2,
    BIND_FAIL = 3,
    BIND_SUCCESS = 4
}

export const useCheckTicket = (ticket: Ref<any>) => {
    const status = useState(() => WxBindStatus.NORMAL)
    const check = async () => {
        try {
            const data = await checkTicket({
                key: ticket.value.key,
                channel: 'bind'
            })
            status.value = data.status
            if (status.value == WxBindStatus.BIND_SUCCESS) {
                bindSuccess(data.user)
                end()
            }
            return data
        } catch (error: any) {
            status.value = WxBindStatus.BIND_FAIL
            end()
        }
    }
    const endCallback = () => {
        status.value = WxBindStatus.INVALID
    }
    const { start, end, result } = usePolling(check, {
        key: 'bind_wx',
        totalTime: 120 * 1000,
        callback: endCallback
    })
    return {
        status,
        start,
        end,
        result
    }
}

const bindSuccess = async (user: any) => {
    location.reload()
}
