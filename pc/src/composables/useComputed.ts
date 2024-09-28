import { computed, ComputedRef } from 'vue'
export function useComputed<T>(fn: (...args: any[]) => T) {
    const cachedValues = new Map()
    return function (...args: any[]): ComputedRef<T> {
        const key = JSON.stringify(args)
        const cacheResult = cachedValues.get(key)
        if (cacheResult) return cacheResult
        const result = computed(() => fn(...args))
        cachedValues.set(key, result)
        return result
    }
}
