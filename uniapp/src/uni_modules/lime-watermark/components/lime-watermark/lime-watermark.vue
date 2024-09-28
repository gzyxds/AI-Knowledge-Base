<template>
	<demo-block title="水印" type="ultra" desc="给页面或某个区域加上水印。">
		<demo-block title="基础">
			<l-watermark content="LimeUi">
				<view class="content">
					<text>这是很重要的内容</text>
					<button @click="onClick">点击</button>
				</view>
			</l-watermark>
		</demo-block>
		<demo-block title="多行">
			<l-watermark :content="['LimeUi', '人生得意须尽欢']" :baseSize="1">
				<view class="content">
					<text>这是很重要的内容</text>
					<button @click="onClick">点击</button>
				</view>
			</l-watermark>
		</demo-block>
		<demo-block title="图片">
			<l-watermark image="https://img10.360buyimg.com/img/jfs/t1/182127/16/37474/11761/64659c31F0cd84976/21f25b952f03a49a.jpg" :width="60" :height="60">
				<view class="content">
					<text>这是很重要的内容</text>
					<button @click="onClick">点击</button>
				</view>
			</l-watermark>
		</demo-block>
	</demo-block>
</template>
<script>
	// https://iconfont.alicdn.com/auth/illus_3d/source/NdzEShoF8VBW/b3a0b9a0-b69a-45ba-bd9c-0388a738f9ca.blend?spm=a313x.illustrations_3d_detail.0.0.35d03a81ks1aAU&auth_key=1710698400-0-0-916c745d62d03b892a148cba160e1275
	import {defineComponent} from '@/uni_modules/lime-shared/vue';
	export default defineComponent({
		setup() {
			
			const onClick = () => {
				console.log('点击')
			}
			return {
				onClick
			}
		}
	})
</script>
<style lang="scss">
	.content {
		box-sizing: border-box;
		height: 500rpx; 
		// width: 100%; 
		background-color: #fff;
		padding: 30rpx;
	}
</style>
