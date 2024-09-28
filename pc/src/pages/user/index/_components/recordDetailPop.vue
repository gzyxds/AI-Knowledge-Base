<template>
  <Popup ref="popRef" width="600px" @close="$emit('close')">
    <el-form label-width="90px" label-position="left">
      <el-form-item label="订单编号">
        {{ data?.sn }}
      </el-form-item>
      <el-form-item label="用户信息">
        {{ data?.user?.nickname }}
      </el-form-item>
      <el-form-item label="操作时间">
        {{ data?.create_time }}
      </el-form-item>
      <el-form-item label="应用名">
        {{ data?.robot_name || '-' }}
      </el-form-item>
      <el-form-item label="变动类型">
        {{ data?.change_type }}
      </el-form-item>
      <el-form-item :label="`变动${type}`">
        <span>{{ data?.action == 1 ? '+' : '-' }}</span
        >{{ data?.change_amount }}
      </el-form-item>
      <el-form-item label="扣费明细" v-if="data?.flows?.length"> </el-form-item>
    </el-form>
    <div class="mt-[-10px]" v-if="data?.flows?.length">
      <el-table :data="data?.flows">
        <el-table-column prop="name" label="模块名称"></el-table-column>
        <el-table-column prop="model" label="AI模型"></el-table-column>
        <el-table-column
          prop="total_price"
          :label="`消耗${appStore.getTokenUnit}`"
        ></el-table-column>
      </el-table>
    </div>
  </Popup>
</template>

<script setup lang="ts">
import { accountDetail } from '@/api/account'
import { useAppStore } from '@/stores/app'

const props = defineProps({
  type: {
    type: Number,
    default: -1
  }
})

const emits = defineEmits(['close'])
const appStore = useAppStore()
const popRef = shallowRef()

const id = ref(-1)

const data: any = ref({})

//获取详情
const getDetail = async () => {
  data.value = await accountDetail({ id: id.value })
}

//打开弹框
const open = async (value: number) => {
  popRef.value.open()
  id.value = value
  await nextTick()
  getDetail()
}

defineExpose({ open })
</script>
