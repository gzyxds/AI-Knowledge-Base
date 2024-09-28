<template>
	<view class="l-watermark class" ref="containerRef">
		<slot></slot>
		<!-- #ifndef H5 || APP-NVUE -->
		<canvas :style="canvasStyle" class="l-watermark__canvas" canvas-id="l-watermark" v-if="isNotCanvas2d && store.canvasShow"></canvas>
		<!-- #endif -->
		<!-- #ifdef APP-NVUE -->
		<web-view 
			v-if="store.canvasShow"
			ref="watermarkRef"
			@pagefinish="onFinished" 
			@error="onerror" 
			@onPostMessage="onMessage" src="/uni_modules/lime-watermark/hybrid/html/index.html"></web-view>
		<image :style="[styles]" :src="store.waterUrl" mode="widthFix"></image>
		<!-- #endif -->
		<!-- #ifndef H5 || APP-NVUE -->
		<view :style="[styles]"></view>
		<!-- #endif -->
	</view>
</template>

<script lang="ts">
	// @ts-nocheck
	import { computed, defineComponent, getCurrentInstance, watch, ref, onMounted, onUnmounted, reactive } from '@/uni_modules/lime-shared/vue';
	import WaterMarkProps from './props';
	// #ifndef APP-NVUE
	import { reRendering , getStyleStr, getPixelRatio, isCanvas2d} from './utils';
	import { useCanvas } from './useCanvas';
	import { useMutationObserver } from './useMutationObserver';
	import { Watermark } from './watermark.js'
	import { addUnit } from '@/uni_modules/lime-shared/addUnit'
	import { createImage } from '@/uni_modules/lime-shared/createImage'
	// #endif
	// #ifdef APP-NVUE
	import {pathToBase64} from '@/uni_modules/lime-shared/pathToBase64'
	import {isBase64} from '@/uni_modules/lime-shared/isBase64'
	// #endif
	const name = 'l-watermark'
	export default defineComponent({
		name,
		props: WaterMarkProps,
		options: {
			virtualHost: true
		},
		externalClasses: ['class'], 
		setup(props) {
			const context = getCurrentInstance()
			const store = reactive({
				waterUrl: "",
				markWidth: 0,
				markLeft: 0,
				markTop: 0,
				canvasShow: true,
				// #ifdef APP-NVUE
				finished: false,
				// #endif
			})
			
			// #ifndef APP-NVUE
			const canvas = useCanvas('l-watermark', {context});
			const isNotCanvas2d = !isCanvas2d;
			const canvasStyle = computed(() => {
				return getStyleStr({
					width: addUnit(canvas.width),
					height: addUnit(canvas.height),
				})
			})
			// #endif
			
			const styles = computed(() => ({
				zIndex: props.zIndex,
				position: props.fullScreen ? 'fixed' : 'absolute',
				left: store.markLeft,
				top: store.markTop,
				right: 0,
				bottom: 0,
				pointerEvents: 'none',
				// #ifndef APP-NVUE
				backgroundRepeat: 'repeat',
				backgroundSize: `${store.markWidth}px`,
				backgroundImage: `url('${store.waterUrl}')`
				// #endif
			}))
			
			// #ifdef H5
			const stopObservation = ref<boolean>(false);
			const containerRef = ref(null)
			const watermarkRef = ref<HTMLDivElement>();
			const destroyWatermark =() => {
				if (watermarkRef.value) {
				    watermarkRef.value.remove();
				    watermarkRef.value = undefined;
				}
			}
			useMutationObserver(containerRef, (mutations : MutationRecord[]) => {
				if (stopObservation.value) {
				    return;
				}
				mutations.forEach((mutation) => {
					if (reRendering(mutation, watermarkRef.value)) {
						destroyWatermark();
						renderWatermark();
					}
				});
			}, {
				childList: true,
				attributes: true,
				subtree: true,
			})
			// #endif
			
			const appendWatermark = (image : string, markWidth : number, gap: any = {}) => {
				store.waterUrl = image
				// #ifndef APP-NVUE
				const {gapX, offsetLeft, gapXCenter, gapYCenter, offsetTop } = gap
				store.markWidth = (gapX + markWidth) * props.baseSize
				store.markLeft = offsetLeft - gapXCenter;
				store.markTop = offsetTop - gapYCenter;
				// #endif
				
				// #ifdef H5
				if (containerRef.value && watermarkRef.value) {
					stopObservation.value = true;
					watermarkRef.value.setAttribute(
						'style',
						getStyleStr(styles.value)
					);
					containerRef.value.$el?.append(watermarkRef.value);
					setTimeout(() => {
					    stopObservation.value = false;
					});
				}
				// #endif
			}
			let watermark = null
			// #ifdef APP-NVUE
			const {screenWidth, screenHeight, pixelRatio} = uni.getSystemInfoSync()
			watermark = {
				async render(props: any) {
					const ref: any = context.refs['watermarkRef'];
					if(!ref) return
					if(props.image && !isBase64(props.image) && !/^http/.test(props.image) && /^\/static/.test(props.image)) {
						props.image = await pathToBase64(props.image)
					}
					const _props = JSON.stringify(Object.assign({}, props, {screenWidth, screenHeight, pixelRatio}));
					ref?.evalJS(`render(${_props})`)
				}
			}
			// #endif
			const renderWatermark = () => {
				// #ifndef APP-NVUE
				if(!watermark) {
					watermark = new Watermark(canvas as HTMLCanvasElement, {
						createImage,
						appendWatermark,
						pixelRatio: getPixelRatio()
					})
				}
				// #endif
				// #ifdef H5
				if (!watermarkRef.value) {
					watermarkRef.value = document.createElement('div');
				}
				// #endif
				if(watermark) {
					watermark.render(props)
				}
			}
			const stopWatch = watch(props, renderWatermark);
			
			const onerror = () => {}
			const onMessage = (e: any) => {
				const {detail:{data: [res]}} = e
				if(res.event == 'appendWatermark') {
					appendWatermark(res.data, 1)
				} 
			}
			const onFinished = () => {
				store.finished = true
				renderWatermark()
			}
			//  #ifndef APP-NVUE
			onMounted(renderWatermark)
			// #endif
			onUnmounted(() => {
				stopWatch && stopWatch()
				store.canvasShow = false
			})
			return {
				store,
				styles,
				// #ifndef H5 || APP-NVUE 
				isNotCanvas2d,
				canvasStyle,
				// #endif
				// #ifdef H5
				containerRef,
				// #endif
				// #ifdef APP-NVUE
				onFinished,
				onerror,
				onMessage
				// #endif
			}
		}
	})
</script>
<style lang="scss">
	.l-watermark {
        height: 100%;
		position: relative;
		&__canvas {
			position: absolute;
			left: 1500rpx;
		}
	}
</style>