interface UrlFun {
    // 接受一个返回音频地址的函数
    (...args: any): Promise<string> | string
}

interface Options {
    onstart?(): void
    onstop?(): void
    onerror?(): void
}

const audioSet = new Set<HTMLAudioElement>()

export const useAudioPlay = (options?: Options) => {
    const { onstart, onstop, onerror } = options || {}
    const audio = shallowRef<HTMLAudioElement | null>(null)
    const audioLoading = ref(false)
    const audioPlaying = ref(false)
    /**
     * @param urlOrFun
     * 接受一个返回音频地址的函数或音频地址
     * @returns
     */
    const play = async (urlOrFun: UrlFun | string, isCache = true) => {
        let fun
        if (typeof urlOrFun === 'string') {
            fun = () => urlOrFun
        } else {
            fun = urlOrFun
        }
        pauseAll()
        if (!audio.value) {
            createAudio()
        }

        if (isCache && audio.value?.src) {
            audio.value.play()
            return
        }
        audioLoading.value = true
        try {
            const audioUrl = await Promise.resolve(fun())
            audio.value!.src = audioUrl!
            audio.value!.play()
        } catch (error) {
            console.error(error)
            onerror?.()
        } finally {
            audioLoading.value = false
        }
    }
    const pause = () => {
        audio.value?.pause()
        audioPlaying.value = false
    }

    const pauseAll = () => {
        audioSet.forEach((audio) => {
            audio.pause()
            audio.currentTime = 0
            // @ts-ignore
            audio.audioPlaying.value = false
        })
    }
    const createAudio = () => {
        audio.value = new Audio()
        // @ts-ignore
        audio.value.audioPlaying = audioPlaying
        audioSet.add(audio.value)
        audio.value.onplay = () => {
            onstart?.()
            audioPlaying.value = true
        }
        audio.value.onended = () => {
            onstop?.()
            audioPlaying.value = false
        }
        audio.value.onerror = () => {
            onerror?.()
            audioPlaying.value = false
        }
    }

    onBeforeUnmount(() => {
        if (audio.value?.src) {
            pauseAll()
        }
        if (audio.value) {
            audioSet.delete(audio.value)
            audio.value = null
        }
    })

    return {
        play,
        audioLoading,
        audioPlaying,
        pause,
        pauseAll
    }
}
