// @ts-nocheck
import type { Ref, ComponentPublicInstance } from '@/uni_modules/lime-shared/vue'
import { watch, getCurrentScope, onScopeDispose } from '@/uni_modules/lime-shared/vue'
type MaybeRef<T> = T | Ref<T>
type VueInstance = ComponentPublicInstance
type MaybeElement = HTMLElement | VueInstance | undefined | null
type MaybeElementRef<T extends MaybeElement = MaybeElement> = MaybeRef<T>
interface UseMutationObserverOptions extends MutationObserverInit { }


function tryOnScopeDispose(fn : () => void) {
	if (getCurrentScope()) {
		onScopeDispose(fn)
		return true
	}
	return false
}

export function useMutationObserver(
	target : MaybeElementRef,
	callback : MutationCallback,
	options : UseMutationObserverOptions = {},
) {
	let observer : MutationObserver | undefined
	const isSupported = typeof window !== 'undefined' && 'MutationObserver' in window
	const cleanup = () => {
		if (observer) {
			observer.disconnect()
			observer = undefined
		}
	}

	const stopWatch = watch(
		target,
		(el: any) => {
			cleanup()
			if (isSupported && window && el) {
				observer = new MutationObserver(callback)
				observer!.observe(el.$el ? el.$el: el, options)
			}
		},
		{ immediate: true },
	)
	const stop = () => {
		cleanup()
		stopWatch()
	}
	tryOnScopeDispose(stop)
	
	 return {
	    isSupported,
	    stop,
	  }
}