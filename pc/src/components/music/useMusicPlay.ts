import { getMusicLists } from '@/api/music'
import { useEventListener } from '@vueuse/core'
const transTime = (time: number) => {
    let minute: string | number = parseInt(String(time / 60))
    let sec = Math.round(time % 60) + ''
    const isM0 = ':'
    if (minute == 0) {
        minute = '00'
    } else if (minute < 10) {
        minute = '0' + minute
    }
    if (sec.length == 1) {
        sec = '0' + sec
    }
    return minute + isM0 + sec
}

export const useMusicPlay = () => {
    const audioCtx = useState(() => new Audio())
    const currentTime = useState(() => 0)
    const duration = useState(() => 0)
    const playing = useState(() => false)
    const idToItem = useState(() => new Map())
    const currentId = useState(() => -1)
    const musicList = useState<any[]>(() => [])
    const getMusic = async () => {
        const data = await getMusicLists({
            page_type: 0,
            status: 2
        })

        idToItem.value.clear()
        data.lists.forEach((item: any, index: number) => {
            idToItem.value.set(item.id, { ...item, index })
        })
        musicList.value = data.lists
    }

    const setMusic = (lists: any) => {
        idToItem.value.clear()
        lists.forEach((item: any, index: number) => {
            idToItem.value.set(item.id, { ...item, index })
        })
        musicList.value = lists
    }

    const onPlay = () => {
        if (!audioCtx.value.paused) {
            playing.value = true
        }
    }

    const togglePlay = () => {
        if (!playing.value) {
            play()
        } else {
            pause()
        }
    }
    const pause = () => {
        audioCtx.value.pause()
    }
    const play = () => {
        audioCtx.value.play()
    }
    const onPause = () => {
        playing.value = false
    }
    const onError = () => {
        onPause()
    }

    const onLoadedMetadata = () => {
        duration.value = audioCtx.value.duration
    }

    const onTimeupdate = () => {
        currentTime.value = audioCtx.value.currentTime
    }

    const setCurrentTime = (time: number) => {
        audioCtx.value.currentTime = time
    }
    const setCurrentId = (id: number) => {
        currentId.value = id
        setTimeout(() => {
            play()
        })
    }
    const prevOrNext = (num: number) => {
        let index = currentMusic.value.index
        if (index === undefined) index = 0
        let newIndex = index + num
        if (newIndex <= 0) {
            newIndex = 0
        }
        if (newIndex >= musicList.value.length - 1) {
            newIndex = musicList.value.length - 1
        }
        playing.value = false
        const item = musicList.value[newIndex]
        setCurrentId(item.id)
    }

    const durationTrans = computed(() => transTime(duration.value))
    const currentTimeTrans = computed(() => transTime(currentTime.value))
    const currentMusic = computed(
        () => idToItem.value.get(currentId.value) || {}
    )
    onBeforeUnmount(() => {
        pause()
        onPause()
        duration.value = 0
        audioCtx.value.src = ''
    })
    if (!audioCtx.value.onplay) {
        audioCtx.value.onloadedmetadata = onLoadedMetadata
        audioCtx.value.onplay = onPlay
        audioCtx.value.onpause = onPause
        audioCtx.value.ontimeupdate = onTimeupdate
        audioCtx.value.onseeking = onTimeupdate
        audioCtx.value.onended = onPause
        audioCtx.value.onerror = onError
    }

    const setUrl = () => {
        const item = idToItem.value.get(currentId.value)
        if (item && audioCtx.value.src !== item.audio_url) {
            audioCtx.value.src = item.audio_url
        }
    }
    watch(currentId, (value) => {
        if (value == -1) {
            audioCtx.value.src = ''
            duration.value = 0
        } else {
            setUrl()
        }
    })
    watch(musicList, setUrl)

    return {
        audioCtx,
        currentTime,
        duration,
        durationTrans,
        currentTimeTrans,
        currentId,
        currentMusic,
        playing,
        musicList,
        pause,
        play,
        getMusic,
        setMusic,
        setCurrentTime,
        prevOrNext,
        togglePlay,
        setCurrentId
    }
}
