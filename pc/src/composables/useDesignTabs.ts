export default function useDesignTabs() {
  const tabsState = useState(() => ({
    current: 'avatar',
    isCollapsed: false,
    tabs: [
      {
        label: '形象',
        icon: 'local-icon-avatar',
        id: 'avatar',
        component: 'Avatar'
      },
      {
        label: '配音',
        icon: 'local-icon-dub',
        id: 'dub',
        component: 'Dub'
      },
      {
        label: '音乐',
        icon: 'local-icon-music',
        id: 'music',
        component: 'Music'
      },
      {
        label: '背景',
        icon: 'local-icon-bg',
        id: 'background',
        component: 'Background'
      },
      {
        label: '文字',
        icon: 'local-icon-texts',
        id: 'text',
        component: 'Text'
      },
      {
        label: '字幕',
        icon: 'local-icon-subtitles',
        id: 'captions',
        component: 'Captions'
      },
      {
        label: '贴图',
        icon: 'local-icon-prospect',
        id: 'maps',
        component: 'Maps'
      },
      {
        label: '前景',
        icon: 'local-icon-prospect',
        id: 'prospect',
        component: 'Prospect'
      }
    ]
  }))
  const include = (tab: string) => {
    return tabsState.value.tabs.some((item) => item.id === tab)
  }
  const changeTabs = (tab: string) => {
    if (!include(tab)) return
    const { pauseAll } = useAudioPlay()
    const router = useRouter()
    const route = useRoute()
    tabsState.value.current = tab
    tabsState.value.isCollapsed = false
    router.replace({
      path: '',
      query: {
        ...route.query,
        currentTab: tab
      }
    })
    pauseAll()
  }
  const initTabs = () => {
    const route = useRoute()
    const currentTab = route.query.currentTab as string
    tabsState.value.current = include(currentTab) ? currentTab : 'avatar'
  }
  return {
    tabsState,
    changeTabs,
    initTabs
  }
}
