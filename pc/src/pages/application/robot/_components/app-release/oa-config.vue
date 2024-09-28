<template>
  <popup
    ref="popupRef"
    title="公众号配置"
    :async="true"
    width="600px"
    confirm-button-text=""
    cancel-button-text=""
  >
    <div class="text-xl text-tx-primary font-medium">添加菜单</div>
    <div class="mt-4">
      <div>1.进入微信<span class="text-success">公众号后台</span></div>
      <div class="text-[#999] mt-2">
        <div>请确保您的公众号已过微信认证</div>
        <div class="flex items-center">
          <span>路径：内容与互动 > 自定义菜单 > 添加菜单</span>

          <el-link
            :href="wxoaConfgMenuImg"
            target="_blank"
            type="primary"
            class="ml-2"
          >
            查看填写示例
          </el-link>
        </div>
      </div>
    </div>
    <div class="mt-6">
      <div>2.创建菜单</div>
      <div class="text-[#999] mt-2">
        <div>填写菜单名称，将以下链接或二维码，配置到菜单里</div>
      </div>
      <div class="flex items-center mt-2">
        <span>{{ link }}</span>
        <span class="ml-2 text-primary cursor-pointer" @click="copy(link)"
          >复制链接</span
        >
      </div>
    </div>

    <div class="text-xl font-medium text-tx-primary mt-[16px]">自动回复</div>
    <div class="mt-4">
      <div>1.进入微信<span class="text-success">公众号后台</span></div>
      <div class="text-[#999] mt-2">
        <div>
          <span>路径：内容与互动 > 自动回复 > 收到消息回复</span>
          <el-link
            :href="wxoaConfgReplyImg"
            target="_blank"
            type="primary"
            class="ml-2"
          >
            查看填写示例
          </el-link>
        </div>
      </div>
    </div>
    <div class="mt-6">
      <div>2.创建自动回复</div>
      <div class="text-[#999] mt-2">
        <div>选择自动回复类型，将以下链接或二维码，配置到回复里</div>
      </div>
      <div class="flex items-center mt-2">
        <span>{{ link }}</span>
        <span class="ml-2 text-primary cursor-pointer" @click="copy(link)"
          >复制链接</span
        >
      </div>
    </div>
  </popup>
</template>

<script setup lang="ts">
import wxoaConfgMenuImg from '@/assets/image/wxoa_config_menu.png'
import wxoaConfgReplyImg from '@/assets/image/wxoa_config_autoreply.png'
const channel = ref('')
const { copy } = useCopy()
const popupRef = shallowRef()
const link = computed(() => `${location.origin}/chat/${channel.value}`)
const open = (sn: string) => {
  channel.value = sn
  popupRef.value?.open()
}
defineExpose({
  open
})
</script>

<style scoped lang="scss"></style>
