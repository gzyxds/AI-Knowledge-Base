import { getSearchConfig, getSearchExample, postSearch } from '@/api/search'
import { ModelEnums, StatusEnums, TypeEnums } from './searchEnums'
import { Sse } from '@/utils/http/sse'
import { useUserStore } from '@/stores/user'
const options = useState(() => ({
    model: ModelEnums.BASE,
    type: TypeEnums.ALL,
    ask: '',
    probe: 0
}))

watch(
    () => options.value.model,
    (value) => {
        if (value !== ModelEnums.STUDY) {
            options.value.type = TypeEnums.ALL
        }
    },
    { flush: 'post', immediate: true }
)
// watch(
//     () => options.value.type,
//     (value) => {
//         if (value !== TypeEnums.ALL) {
//             options.value.model = ModelEnums.STUDY
//         }
//     },
//     { flush: 'post', immediate: true }
// )
let sse: Sse
export const useSearch = () => {
    const result = useState(() => ({
        query: '',
        data: [] as any[],
        status: -1,
        search: [] as any[],
        outline: {} as any,
        outline_json: {} as any
    }))
    const isSearching = useState(() => false)
    const showSearchResult = useState(() => false)
    const useStore = useUserStore()
    const config = useState(() => ({
        status: 0,
        price: 0,
        isVipFree: false
    }))

    const getConfig = async () => {
        config.value = await getSearchConfig()
    }
    const launchSearch = async (text = '') => {
        if (text) {
            options.value.ask = text
        }
        if (!useStore.isLogin) return useStore.toggleShowLogin()
        if (!options.value.ask) return feedback.msgError('请输入你想搜索的问题')
        if (isSearching.value) return feedback.msgWarning('正在搜索中...')
        isSearching.value = true

        showSearchResult.value = true
        sse = postSearch({ ...options.value, stream: true })
        result.value.query = options.value.ask
        result.value.data = []
        result.value.status = StatusEnums.ANALYSIS
        result.value.search = []
        result.value.outline = {}
        result.value.outline_json = {}
        const pushData = (type: string, target: string, data: any) => {
            const current = result.value.data.find(
                (item) => item.type == 'markdown' && item.target == 'update'
            )
            if (current) {
                if (isString(current.content)) {
                    current.content += data
                }
                if (isArray(current.content)) {
                    current.content.push(data)
                }
                current.target = target
            } else {
                result.value.data.push({
                    type: type,
                    target: target,
                    content: data
                })
            }
        }

        sse.onmessage = ({ data: dataJson }: any) => {
            const { card_type, target, data } = dataJson
            switch (card_type) {
                case 'error':
                    feedback.msgError(data)
                    isSearching.value = false
                    showSearchResult.value = false
                    break
                case 'action': {
                    result.value.status = StatusEnums.SEARCH
                    break
                }
                case 'markdown': {
                    result.value.status = StatusEnums.SUMMARY
                }
                // eslint-disable-next-line no-fallthrough
                case 'expand_query':
                case 'search_result':
                case 'suggestion': {
                    pushData(card_type, target, data)
                    break
                }
                case 'outline_json': {
                    result.value.outline_json = data
                    break
                }
                case 'outline': {
                    result.value.outline = data
                    break
                }
                case 'done': {
                    result.value.status = StatusEnums.SUCCESS + 1
                    const searchResult = result.value.data.findLast(
                        (item) => item.type === 'search_result'
                    )
                    result.value.search =
                        searchResult?.content?.map(
                            (item: any, index: number) => ({
                                ...item,
                                index: index + 1
                            })
                        ) || []
                    useStore.getUser()
                    getConfig()
                    break
                }
            }
        }
        sse.onerror = () => {
            isSearching.value = false
            showSearchResult.value = false
        }
        sse.onclose = () => {
            isSearching.value = false
        }
    }

    const abortSearch = () => {
        sse?.abort()
    }
    return {
        config,
        getConfig,
        showSearchResult,
        options,
        result,
        launchSearch,
        abortSearch
    }
}

export const useSearchEx = () => {
    const searchEx = ref([])
    const getSearchEx = async () => {
        searchEx.value = await getSearchExample()
    }

    return {
        searchEx,
        getSearchEx
    }
}
