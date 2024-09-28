// @ts-nocheck
interface CSSProperties {
	[key: string]: string | number
}
/** converting camel-cased strings to be lowercase and link it with Separato */
export function toLowercaseSeparator(key: string) {
  return key.replace(/([A-Z])/g, '-$1').toLowerCase();
}

export function getStyleStr(style: CSSProperties): string {
  return Object.keys(style)
    .map((key: string) => `${toLowercaseSeparator(key)}: ${style[key]};`)
    .join(' ');
}
const {pixelRatio, platform} =  uni.getSystemInfoSync()
export const isCanvas2d = uni.canIUse('createOffscreenCanvas') && Boolean(uni.createOffscreenCanvas) && !/window|mac/.test(platform);
/** Returns the ratio of the device's physical pixel resolution to the css pixel resolution */
export function getPixelRatio() {
	// #ifndef H5
	return !isCanvas2d ? 1 : pixelRatio
	// #endif
	// #ifndef H5
	return pixelRatio
	// #endif
}

/** Whether to re-render the watermark */
export const reRendering = (mutation: MutationRecord, watermarkElement?: HTMLElement) => {
  let flag = false;
  // Whether to delete the watermark node
  if (mutation.removedNodes.length) {
    flag = Array.from(mutation.removedNodes).some((node) => node === watermarkElement);
  }
  // Whether the watermark dom property value has been modified
  if (mutation.type === 'attributes' && mutation.target === watermarkElement) {
    flag = true;
  }
  return flag;
};