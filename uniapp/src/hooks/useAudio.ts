import { isWeixinClient } from '@/utils/client'
import { onBeforeUnmount, ref, shallowRef } from 'vue'
//#ifdef H5
import { Howl, Howler } from 'howler'
//#endif
const audioCtxs = new Set<UniApp.InnerAudioContext>()

interface Options {
    onstart?(): void
    onstop?(): void
    onerror?(): void
}

export const useAudio = (options?: Options) => {
    const { onstart, onstop, onerror } = options || {}
    const audioCtx = shallowRef<UniApp.InnerAudioContext | null>(null)

    const isPlaying = ref(false)
    const duration = ref(0)
    const currentTime = ref(0)
    const timer = ref(0)
    const _paly = async () => {
        //#ifdef H5
        //@ts-ignore
        audioCtx.value.off('load')
        //@ts-ignore
        audioCtx.value.load()

        //@ts-ignore
        audioCtx.value.once('load', () => {
            //@ts-ignore
            duration.value = audioCtx.value!.duration()

            if (isWeixinClient()) {
                window.WeixinJSBridge.invoke(
                    'getNetworkType',
                    {},
                    () => {
                        audioCtx.value!.play()
                    },
                    false
                )
            } else {
                audioCtx.value!.play()
            }
        })

        //#endif
        //#ifndef H5
        audioCtx.value!.onCanplay(() => {
            console.log(audioCtx.value!.paused)
            audioCtx.value!.play()
        })
        audioCtx.value!.play()
        //#endif
    }
    const onPlay = () => {
        onstart?.()
        onTimeUpdate()
        isPlaying.value = true
    }
    const onStop = () => {
        onstop?.()
        isPlaying.value = false
        clearTimeout(timer.value)
    }

    const onError = (e: any) => {
        console.error(e)
        onerror?.()
        isPlaying.value = false
        clearTimeout(timer.value)
    }

    const onTimeUpdate = () => {
        // #ifndef H5
        duration.value = audioCtx.value!.duration
        currentTime.value = audioCtx.value!.currentTime
        // #endif
        // #ifdef H5
        //@ts-ignore
        currentTime.value = audioCtx.value!.seek()
        clearTimeout(timer.value)
        timer.value = setTimeout(() => {
            onTimeUpdate()
        }, 100)
        // #endif
    }

    const seek = (time: number) => {
        audioCtx.value?.seek(time)
        //@ts-ignore
        currentTime.value = audioCtx.value!.seek()
    }
    const createAudio = (src: string) => {
        // #ifdef H5
        //@ts-ignore
        audioCtx.value = new Howl({
            src,
            autoplay: false,
            preload: false
        })
        //@ts-ignore
        audioCtx.value.isPlaying = isPlaying

        //@ts-ignore
        audioCtx.value.on('play', onPlay)
        //@ts-ignore
        audioCtx.value.on('end', onStop)
        //@ts-ignore
        audioCtx.value.on('loaderror', onError)
        //@ts-ignore
        audioCtx.value.on('playerror', onError)
        //@ts-ignore
        audioCtx.value.on('stop', onStop)
        //@ts-ignore
        audioCtx.value.on('pause', onStop)
        // #endif

        // #ifndef H5
        audioCtx.value = uni.createInnerAudioContext()
        audioCtx.value.obeyMuteSwitch = false
        //@ts-ignore
        audioCtx.value.isPlaying = isPlaying
        audioCtx.value.onPlay(onPlay)
        audioCtx.value.onEnded(onStop)
        audioCtx.value.onError(onError)
        audioCtx.value.onStop(onStop)
        audioCtx.value.onPause(onStop)
        audioCtx.value.onTimeUpdate(onTimeUpdate)
        audioCtx.value.onSeeking(onTimeUpdate)
        // #endif
    }

    const destroy = () => {
        if (audioCtx.value) {
            audioCtx.value.destroy?.()
            audioCtxs.delete(audioCtx.value)
            audioCtx.value = null
            clearTimeout(timer.value)
        }
    }
    const setUrl = (src: string) => {
        // #ifdef H5
        //@ts-ignore
        audioCtx.value?.unload()
        createAudio(src)
        //#endif
        // #ifndef H5
        if (!audioCtx.value) {
            createAudio(src)
        }
        //#endif
        audioCtx.value!.src = src
    }
    const play = async (src?: string) => {
        pauseAll()
        if (src) {
            createAudio(src)
            setUrl(src)
        }
        await _paly()
    }

    const suspend = () => {
        audioCtx.value?.pause()
    }

    const pause = () => {
        audioCtx.value?.stop()
    }

    const pauseAll = () => {
        audioCtxs.forEach((audio) => {
            if (!audio.paused) {
                audio.pause()
            }
        })
    }
    onBeforeUnmount(() => {
        if (isPlaying.value) {
            pause()
        }
        destroy()
    })
    return {
        pause,
        pauseAll,
        play,
        duration,
        currentTime,
        isPlaying,
        setUrl,
        suspend,
        seek
    }
}
