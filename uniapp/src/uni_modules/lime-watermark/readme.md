# lime-watermark 水印
- 给页面或某个区域加上水印。
- 支持uniapp/uniappx(web,ios,安卓)

## 使用
- 导入插件后直接使用
 
```html
<l-watermark content="LimeUi">
	<view class="content">
		<text>这是很重要的内容</text>
		<button @click="onClick">点击</button>
	</view>
</l-watermark>
```

### 多行

```html
<l-watermark :content="['LimeUi', '人生得意']"  :baseSize="1">
	<view class="content">
		<text>这是很重要的内容</text>
		<button @click="onClick">点击</button>
	</view>
</l-watermark>
```

### 图片
- **注意**:`uvue`不支持
- 使用`width`和`height`控制图片的大小
- 注意，网络图片在`H5`和`NVUE`需要解决`跨域`问题，在`小程序`需要去公众平台配置`download`域名

```html
<l-watermark image="https://img10.360buyimg.com/img/jfs/t1/182127/16/37474/11761/64659c31F0cd84976/21f25b952f03a49a.jpg" :width="60" :height="60">
	<view class="content">
		<text>这是很重要的内容</text>
		<button @click="onClick">点击</button>
	</view>
</l-watermark>
```

### 查看示例
- 导入后直接使用这个标签查看演示效果

```html
// 代码位于 uni_modules/lime-watermark/compoents/lime-watermark
<lime-watermark />
```

### 插件标签
- 默认 l-watermark 为 component
- 默认 lime-watermark 为 demo


## API

### Props

| 参数                       | 说明                                                         | 类型             | 默认值       |
| --------------------------| ------------------------------------------------------------ | ---------------- | ------------ |
| zIndex                    | 追加的水印元素的 z-index                                       | <em>number</em>  | `9`        |
| content                   | 水印文字内容                                                    | <em>string，string[]</em>  | `-`        |
| width                     | 水印的宽度，`content` 的默认值为自身的宽度(px)                    | <em>number</em>  | `120`     |
| height           		    | 水印的高度，`content` 的默认值为自身的高度(px)                    | <em>number</em>  | `64`      |
| image           	        | 图片源，建议导出 2 倍或 3 倍图，优先级高 (支持 base64 格式)       | <em>string</em>  | `-`      |
| rotate           	        | 水印绘制时，旋转的角度，单位           	                       | <em>number</em>  | `-22`  |
| gap             	        | 水印之间的间距        					                     	| <em>[number, number]</em>  | `[30,30]`  |
| offset             	    | 水印距离容器左上角的偏移量，默认为 `gap/2 `    					| <em>number, number]</em>  | `-`  |
| font                  	| 文字样式 `{color, fontSize, fontFamily}  `     				| <em>Font</em>  | `-`  |
| fontGap             	    | 多行文字的间隔      					                    	| <em>number</em>  | `3`  |
| fullScreen             	| 是否为全屏水印      					                    	| <em>boolean</em>  | `false`  |
| baseSize             		| 图片每块内容重复次数   					                    	| <em>number</em>  | `2`  |



## 打赏

如果你觉得本插件，解决了你的问题，赠人玫瑰，手留余香。  
![](https://testingcf.jsdelivr.net/gh/liangei/image@1.9/alipay.png)
![](https://testingcf.jsdelivr.net/gh/liangei/image@1.9/wpay.png)