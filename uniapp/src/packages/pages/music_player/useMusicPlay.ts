import { onBeforeUnmount, onMounted, ref, shallowRef } from 'vue'

export const useMusicPlay = () => {
    const audioCtx = shallowRef(uni.createInnerAudioContext())
    const isPlaying = ref(false)
    const duration = ref(0)
    const currentTime = ref(0)
    const timer = ref(0)
    audioCtx.value.obeyMuteSwitch = false

    const onPlay = () => {
        onTimeUpdate()
        isPlaying.value = true
    }
    const onStop = () => {
        isPlaying.value = false
        clearTimeout(timer.value)
    }

    const onError = () => {
        isPlaying.value = false
        clearTimeout(timer.value)
    }

    const onTimeUpdate = () => {
        duration.value = audioCtx.value!.duration
        currentTime.value = audioCtx.value!.currentTime
    }

    const seek = (time: number) => {
        audioCtx.value?.seek(time)
    }

    const destroy = () => {
        if (audioCtx.value) {
            audioCtx.value.onPlay?.(onPlay)
            audioCtx.value.onEnded?.(onStop)
            audioCtx.value.onError?.(onError)
            audioCtx.value.onStop?.(onStop)
            audioCtx.value.onPause?.(onStop)
            audioCtx.value.onTimeUpdate?.(onTimeUpdate)
            audioCtx.value.onSeeking?.(onTimeUpdate)
            clearTimeout(timer.value)
        }
    }
    const setUrl = (src: string) => {
        audioCtx.value!.src = src
    }
    const play = async () => {
        audioCtx.value!.onCanplay(() => {
            console.log(audioCtx.value!.paused)
            audioCtx.value!.play()
        })
        audioCtx.value!.play()
    }

    const pause = () => {
        audioCtx.value?.pause()
    }
    onMounted(() => {
        audioCtx.value.onPlay(onPlay)
        audioCtx.value.onEnded(onStop)
        audioCtx.value.onError(onError)
        audioCtx.value.onStop(onStop)
        audioCtx.value.onPause(onStop)
        audioCtx.value.onTimeUpdate(onTimeUpdate)
        audioCtx.value.onSeeking(onTimeUpdate)
    })
    onBeforeUnmount(() => {
        if (isPlaying.value) {
            pause()
        }
        destroy()
    })
    return {
        pause,
        play,
        duration,
        currentTime,
        isPlaying,
        setUrl,
        seek
    }
}
