import { ref } from 'vue'

interface Options {
    key?: string
    time?: number
    totalTime?: number
    count?: number
    callback?(): void
}
const pollingDict: any = {}

export default function usePolling(fun: () => Promise<any>, options: Options) {
    const {
        key,
        time = 2000,
        totalTime,
        count,
        callback = () => false
    } = options ?? ({} as Options)

    let timer: any = null
    let endTime: number | null = null
    let totalCount = 0

    const result = ref(null)
    const error = ref(null)

    const run = () => {
        if (endTime && endTime <= Date.now()) {
            end()
            callback()
            return
        }
        if (count && totalCount >= count) {
            end()
            callback()
            return
        }
        totalCount++
        timer = setTimeout(() => {
            fun()
                .then((res) => {
                    result.value = res
                    run()
                })
                .catch((err) => {
                    error.value = err
                })
        }, time)
    }

    const start = () => {
        end() // add this line
        if (key && pollingDict[key]) {
            pollingDict[key].end()
            delete pollingDict[key]
        }
        endTime = totalTime ? Date.now() + totalTime : null
        run()
        if (key) {
            pollingDict[key] = { end }
        }
    }

    const end = () => {
        if (timer) {
            setTimeout(() => {
                clearTimeout(timer)
                timer = null
                endTime = null
                totalCount = 0
                if (key) delete pollingDict[key]
            }, 0)
        }
    }

    return {
        start,
        end,
        error,
        result
    }
}
