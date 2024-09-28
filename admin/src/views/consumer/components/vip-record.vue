<template>
    <Popup ref="popRef" title="会员开通记录" width="800px">
        <el-table :data="vipRecordList" height="500px">
            <el-table-column label="会员等级" prop="package_name" />
            <el-table-column label="会员时长" prop="original_package_long_time" />
            <el-table-column label="到期时间" prop="package_long_time" />
            <el-table-column label="购买来源" prop="channel_text" />
            <el-table-column label="退款状态" prop="refund_status_desc">
                <template #default="{ row }">
                    <el-tag type="danger" v-if="row.refund_status">
                        {{ row.refund_status_text }}
                    </el-tag>
                    <el-tag type="warning" v-else>
                        {{ row.refund_status_text }}
                    </el-tag>
                </template>
            </el-table-column>
<!--            <el-table-column label="操作人" prop="refund_status_desc"></el-table-column>-->
            <el-table-column label="记录时间" prop="pay_time_text" sortable>
                <template #default="{ row }">
                    {{ row.pay_time_text || row.create_time }}
                </template>
            </el-table-column>
        </el-table>
    </Popup>
</template>

<script lang="ts" setup>
import Popup from '@/components/popup/index.vue'
import {getOpenVipRecord} from '@/api/consumer'

const popRef = shallowRef()
const route = useRoute()
const vipRecordList = ref<any[]>([])

const open = async () => {
    await nextTick()
    popRef.value?.open()
    await getData()
}

const getData = async () => {
    try {
        vipRecordList.value = await getOpenVipRecord({
            user_id: route.query.id
        })
    } catch (e) {
    }
}

defineExpose({open})
</script>
