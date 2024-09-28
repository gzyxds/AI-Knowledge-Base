<template>
  <div class="flex flex-wrap mx-[-8px]">
    <div
      v-for="(item, index) in payWayList"
      :key="index"
      class="flex items-center px-[35px] py-[20px] mx-[8px] mt-[10px] rounded-lg inactive cursor-pointer bg-body"
      :class="{
        active: payWay == item.pay_way
      }"
      @click="payWay = item.pay_way"
    >
      <ElImage :src="item.icon" class="h-[24px] w-[24px]" />
      <div class="ml-[10px]">
        {{ item.name }}
      </div>
      <div v-if="payWay == item.pay_way" class="select-icon">
        <Icon class="el-icon-select" name="el-icon-Select" />
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import { getPayWayList } from '@/api/pay'
const props = defineProps<{
  from: 'recharge'
  modelValue: string | number
}>()

const emit = defineEmits<{
  (event: 'update:modelValue', value: string | number): void
}>()
const payWay = useVModel(props, 'modelValue', emit)
const payWayList = ref<any[]>([])
const getPayWay = async () => {
  const data = await getPayWayList({
    from: props.from
  })
  payWayList.value = data.lists
  let selectIndex = payWayList.value.findIndex((item) => item.is_default == 1)
  if (selectIndex === -1) selectIndex = 0
  payWay.value = payWayList.value[selectIndex]?.pay_way || '-1'
}
getPayWay()
</script>

<style lang="scss" scoped>
.active {
  border: 1px solid var(--el-color-primary) !important;
}
.inactive {
  position: relative;
  overflow: hidden;
  border: 1px solid;
  @apply border-br-light;
}
.select-icon {
  $size: 26px;
  position: absolute;
  right: -1px;
  bottom: -1px;
  width: $size;
  height: $size;
  display: flex;
  align-items: center;
  justify-content: center;
  clip-path: polygon(0 100%, 100% 0, 100% 100%);
  :deep() .el-icon-select {
    transform-origin: center center;
    transform: translate(35%, 35%);
  }
  @apply text-white bg-primary;
}
</style>
