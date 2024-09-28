<template>
    <view class="p-[20rpx]">
        <view class="bg-white rounded-lg p-[30rpx]">
            <view class="flex items-center">
                <view class="text-[#555555] text-base w-[190rpx]"
                    >订单编号</view
                >
                <view class="text-[#333333] text-base flex-1">{{
                    detailData?.sn
                }}</view>
            </view>
            <view class="flex items-center mt-[40rpx]">
                <view class="text-[#555555] text-base w-[190rpx]"
                    >用户信息</view
                >
                <view class="text-[#333333] text-base flex-1">{{
                    detailData?.user.nickname
                }}</view>
            </view>
            <view class="flex items-center mt-[40rpx]">
                <view class="text-[#555555] text-base w-[190rpx]"
                    >操作时间</view
                >
                <view class="text-[#333333] text-base flex-1">{{
                    detailData?.create_time
                }}</view>
            </view>
            <view class="flex items-center mt-[40rpx]">
                <view class="text-[#555555] text-base w-[190rpx]"
                    >智能体/应用名</view
                >
                <view class="text-[#333333] text-base flex-1">{{
                    detailData?.robot_name
                }}</view>
            </view>
            <view class="flex items-center mt-[40rpx]">
                <view class="text-[#555555] text-base w-[190rpx]"
                    >变动类型</view
                >
                <view class="text-[#333333] text-base flex-1">{{
                    detailData?.change_type
                }}</view>
            </view>
            <view class="flex items-center mt-[40rpx]">
                <view class="text-[#555555] text-base w-[190rpx]"
                    >变动数量</view
                >
                <view class="text-[#333333] text-base flex-1">
                    {{ detailData?.action == 1 ? '+' : '-' }}
                    {{ detailData?.change_amount }}
                </view>
            </view>
            <view class="mt-[40rpx]" v-if="Number(type) === 1">
                <view class="text-[#555555] text-base w-[190rpx]"
                    >扣费明细</view
                >
                <view class="mt-2">
                    <uni-table border stripe emptyText="暂无更多数据">
                        <!-- 表头行 -->
                        <uni-tr>
                            <uni-th align="center">模块名称</uni-th>
                            <uni-th align="center">AI模型</uni-th>
                            <uni-th align="center"
                                >消耗{{ appStore.getTokenUnit }}</uni-th
                            >
                        </uni-tr>
                        <!-- 表格数据行 -->
                        <uni-tr
                            v-for="(item, index) in detailData?.flows"
                            :key="index"
                        >
                            <uni-td>{{ item.name }}</uni-td>
                            <uni-td>{{ item.model }}</uni-td>
                            <uni-td>{{ item.total_price }}</uni-td>
                        </uni-tr>
                    </uni-table>
                </view>
            </view>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { onLoad } from '@dcloudio/uni-app'
import { ref } from 'vue'
import { accountDetail } from '@/api/user'
import { useAppStore } from '@/stores/app'
import { useRoute } from 'uniapp-router-next'

const appStore = useAppStore()
const route = useRoute()
const id = ref('')

const type = route.query.type
const detailData = ref()

const tableData = ref([])

const getData = async () => {
    detailData.value = await accountDetail({ id: id.value })
}

const getTable = async () => {}

onLoad((option: any) => {
    id.value = option.id
    getData()
})
</script>

<style lang="scss" scoped></style>
