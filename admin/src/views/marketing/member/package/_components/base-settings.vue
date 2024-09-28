<template>
    <div>
        <div class="border-b border-solid border-b-br-light">
            <div class="font-medium text-xl mb-4">基础信息</div>

            <el-form-item label="等级名称" prop="name">
                <div>
                    <el-input
                        class="w-80"
                        v-model="formData.name"
                        placeholder="请输入等级名称"
                        :clearable="true"
                    />
                    <div class="form-tips">
                        修改等级名称，相关联的用户也会跟着调整，将会显示最新设置等级名称
                    </div>
                </div>
            </el-form-item>

            <el-form-item label="等级描述" prop="describe">
                <div class="w-80">
                    <el-input
                        v-model="formData.describe"
                        placeholder="请输入等级描述"
                        :clearable="true"
                    />
                </div>
            </el-form-item>

            <el-form-item label="排序" prop="sort">
                <div class="w-80">
                    <el-input
                        v-model="formData.sort"
                        placeholder="请输入排序"
                        :clearable="true"
                    />
                </div>
            </el-form-item>

            <el-form-item label="是否上架" prop="status">
                <div class="w-80">
                    <el-switch
                        v-model="formData.status"
                        :inactive-value="0"
                        :active-value="1"
                    />
                </div>
            </el-form-item>
        </div>

        <div>
            <div class="font-medium text-xl my-4">会员套餐</div>
            <el-table ref="memberTableRef" row-key="duration" :data="formData.price_list">
                <el-table-column width="50">
                    <template #default>
                        <div class="move-icon cursor-move">
                            <Icon name="el-icon-Rank" />
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="会员时长" prop="duration" min-width="100">
                    <template #default="{ row }">
                        {{ durationMap[row.duration_type] ? row.duration + durationMap[row.duration_type] : '永久' }}
                    </template>
                </el-table-column>
                <el-table-column label="销售价格" prop="sell_price" min-width="100" />
                <el-table-column label="购买人数" prop="buy_num" min-width="100" />
                <el-table-column label="会员标签" prop="tags" min-width="100" />
                <el-table-column label="额外赠送" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_give" type="success">开启</el-tag>
                        <el-tag v-else type="danger">关闭</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="赠送电力值" prop="give_balance" min-width="100" />
                <el-table-column label="赠送智能体" prop="give_robot" min-width="100" />
                <el-table-column label="是否推荐" min-width="100">
                    <template #default="{ row, $index }">
                        <el-switch
                            v-model="row.is_recommend"
                            :active-value="1"
                            :inactive-value="0"
                            @change="recommendChange($event, $index)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="是否上架" min-width="100">
                    <template #default="{ row }">
                        <el-switch v-model="row.status" :active-value="1" :inactive-value="0" />
                    </template>
                </el-table-column>
                <el-table-column label="操作" min-width="120" fixed="right">
                    <template #default="{ row, $index }">
                        <ElButton type="primary" link @click="packageEdit(row, $index)">
                            编辑
                        </ElButton>
                        <ElButton type="danger" link @click="packageDel($index)">
                            删除
                        </ElButton>
                    </template>
                </el-table-column>
            </el-table>
            <el-button type="primary" class="mt-4" size="large" @click="packageAdd">添加</el-button>
        </div>

        <!--    会员套餐 【添加 / 编辑】 弹窗    -->
        <PackagePop
            v-if="showPackagePop"
            ref="packagePopRef"
            @success="handlePackage"
            @close="showPackagePop = false"
        ></PackagePop>
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import Sortable from 'sortablejs'
import type { ElTable } from 'element-plus'
import type {MemberRequest, PriceList} from '@/api/marketing/member_d';
import PackagePop from './_components/package-pop.vue'

interface Props { modelValue: MemberRequest }
const props = defineProps<Props>()

const emit = defineEmits(['update:modelValue'])
const { modelValue: formData } = useVModels(props, emit)

const memberTableRef = ref<InstanceType<typeof ElTable>>()
const packagePopRef = shallowRef<InstanceType<typeof PackagePop>>();

const showPackagePop = ref<boolean>(false)
const packageEditNum = ref<number>(-1)
// 会员时长映射map
const durationMap = { "1": "天", "2": "个月"}


// ——————————————————————添加会员套餐
const packageAdd = async () => {
    showPackagePop.value = true
    await nextTick()
    packagePopRef.value?.open('add')
}

// ——————————————————————编辑会员套餐
const packageEdit = async (row: PriceList, index: number) => {
    showPackagePop.value = true
    packageEditNum.value = index
    await nextTick()
    packagePopRef.value?.open('edit', row)
}

// ——————————————————————删除会员套餐
const packageDel = (index: number) => {
    formData.value.price_list.splice(index, 1)
}

// ——————————————————————处理会员套餐的 添加 / 编辑
const handlePackage = ({type, row}: {type: string, row: PriceList}) => {
    console.log(type, row)
    if (type === 'add') {
        formData.value.price_list.push(row)
    } else {
        const i = packageEditNum.value
        formData.value.price_list[i] = row
    }
}

const recommendChange = (status: number, index: number) => {
    console.log(status, index)
    formData.value.price_list.forEach((item: PriceList) => {
        item.is_recommend = 0;
    });
    formData.value.price_list[index].is_recommend = 1
}

// —————————————————————————————初始化拖拽排序的插件
const initSortable = () => {
    const el = memberTableRef.value?.$el.querySelector('.el-table__body tbody')
    Sortable.create(el, {
        animation: 150,
        handle: '.move-icon',
        onEnd: ({ newIndex, oldIndex }: any) => {
            console.log(newIndex, oldIndex)
            const arr = formData.value?.price_list
            const currRow = arr.splice(oldIndex, 1)[0]
            arr.splice(newIndex, 0, currRow)
            console.log(arr)
            formData.value.price_list = arr
            console.log(formData.value.price_list)
        }
    })
}
onMounted(async () => { await nextTick(); initSortable() })
</script>