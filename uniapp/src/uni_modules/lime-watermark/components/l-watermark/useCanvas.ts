// @ts-nocheck
import { reactive } from '@/uni_modules/lime-shared/vue'
import { isCanvas2d } from './utils'

interface UseCanvasOptions {
	context?: any
	width?: number
	height?: number
}

export function useCanvas(canvasId: string, options: UseCanvasOptions = {}) {
	const { context, width = 300, height = 150 } = options
	if(typeof document !== 'undefined') {
		return document.createElement('canvas')
	} else if(isCanvas2d) {
		return uni.createOffscreenCanvas({type: '2d', width, height})
	} else {
		const ctx = uni.createCanvasContext(canvasId, context)
		return reactive({
			getContext(type: string) {
				if(type == '2d') {
					return ctx
				}
			},
			toDataURL() {
				return new Promise((resolve, reject) => {
					const key = 'toTempFilePath'
					// 钉钉小程序
					if(key in ctx) {
						ctx[key]({
							success(res: any) {
								resolve(res.filePath)
							},
							fail(err: any) {
								reject(err)
							}
						})
					} else {
						uni.canvasToTempFilePath({
							canvasId,
							success(res) {
								resolve(res.tempFilePath)
							},
							fail(err) {
								reject(err)
							}
						}, context)
					}
					
				})
			},
			width: 300,
			height: 150,
		})
	}
}
