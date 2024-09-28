// @ts-nocheck
import {PropType} from './vue'

export interface WatermarkProps {
  zIndex?: number;
  rotate?: number;
  width?: number;
  height?: number;
  image?: string;
  content?: string | string[];
  font?: {
    color?: string;
    fontSize?: number | string;
    fontWeight?: 'normal' | 'light' | 'weight' | number;
    fontStyle?: 'none' | 'normal' | 'italic' | 'oblique';
    fontFamily?: string;
  };
  gap?: [number, number];
  offset?: [number, number];
}


export default  {
	zIndex: {
		type: Number,
		default: 9
	},
	rotate: {
		type: Number,
		default: -22
	},
	width: Number,
	height: Number,
	image: String,
	content: [String, Array] as PropType<string | string[]>,
	font: {
		type: Object as PropType<WatermarkProps['font']>,
	},
	gap:  {
		type: Array as PropType<number[]>,
		default: () => [30, 30]
	},
	offset: Array as PropType<number[]>,
	/**是否为全屏水印*/
	fullScreen: Boolean,
	/**图片内容重复次数*/
	baseSize: {
		type: Number,
		default: 2
	},
	/**行间隔*/
	fontGap: {
		type: Number,
		default: 3
	}
}