<template>
    <div>
        <el-form label-width="120px" ref="formRef">
            <el-form-item label="模型设置">
                <el-table ref="tableRef" size="large" row-key="name" :data="dataVm">
                    <el-table-column width="50">
                        <template #default>
                            <div class="move-icon cursor-move">
                                <Icon name="el-icon-Rank" />
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="序号" width="60">
                        <template #default="{ $index }">
                            {{ $index + 1 }}
                        </template>
                    </el-table-column>
                    <el-table-column label="模型名称" prop="name" min-width="200">
                        <template #default="{ row }">
                            <el-select
                                class="w-full"
                                :model-value="row.name"
                                filterable
                                @change="modelChange($event, row)"
                            >
                                <el-option
                                    v-for="item in newModels"
                                    :value="item.model"
                                    :label="item.name"
                                    :key="item.model"
                                    :disabled="item.disabled && item.model !== row.name"
                                />
                            </el-select>
                        </template>
                    </el-table-column>
                    <el-table-column label="别名" prop="alias" min-width="180">
                        <template #default="{ row }">
                            <el-input v-model="row.alias" placeholder="为空时显示默认名字" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="price" min-width="160">
                        <template #header>
                            <div class="flex items-center">1000字符消耗电力值</div>
                        </template>

                        <template #default="{ row }">
                            <el-input
                                v-model="row.price"
                                placeholder="为空默认为0，支持3位小数点"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column label="状态" width="100">
                        <template #default="{ row }">
                            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" />
                        </template>
                    </el-table-column>

                    <el-table-column label="操作" width="100" fixed="right">
                        <template #default="{ row, $index }">
                            <ElButton type="danger" link @click="modelDelete(row, $index)">
                                删除
                            </ElButton>
                        </template>
                    </el-table-column>
                </el-table>
            </el-form-item>
            <el-form-item>
                <ElButton
                    type="primary"
                    link
                    @click="modelAdd"
                    :disabled="dataVm.length === newModels.length"
                >
                    +添加模型
                </ElButton>
            </el-form-item>
        </el-form>
    </div>
</template>
<script setup lang="ts">
import { checkModelCanDel } from '@/api/ai_setting/model'
import feedback from '@/utils/feedback'
import { useVModels } from '@vueuse/core'
import { cloneDeep } from 'lodash-es'
import Sortable from 'sortablejs'
const props = defineProps<{
    data: any[]
    models: any[]
}>()
const emit = defineEmits<{
    (event: 'update:data', data: any[]): void
}>()

const { data: dataVm } = useVModels(props, emit)
const tableRef = shallowRef()
const initSortable = () => {
    const el = tableRef.value.$el.querySelector('.el-table__body tbody')
    Sortable.create(el, {
        animation: 150,
        handle: '.move-icon',
        onEnd: ({ newIndex, oldIndex }: any) => {
            console.log(newIndex, oldIndex)
            const arr = dataVm.value
            const currRow = arr.splice(oldIndex, 1)[0]
            arr.splice(newIndex, 0, currRow)
            dataVm.value = arr
        }
    })
}

const newModels = computed<any[]>(() => {
    const models = Object.values(cloneDeep(props.models)) || []
    models.forEach((model: any) => {
        const index = dataVm.value.findIndex((item: any) => item.name === model.model)
        if (index !== -1) {
            model.disabled = true
        }
    })
    return models
})
const modelChange = (model: any, row: any) => {
    const selectModel = newModels.value.find((item) => item.model === model)
    row.channel = selectModel.channel
    row.name = selectModel.model
    row.alias = selectModel.name
}

const modelAdd = () => {
    const item = newModels.value.find((item) => !item.disabled)
    console.log(newModels)
    if (item) {
        dataVm.value.push({
            channel: item.channel,
            name: item.model,
            alias: item.name,
            price: '',
            status: 0
        })
    }
}

const modelDelete = async (row: any, index: number) => {
    const data = await checkModelCanDel({
        model: row.name
    })
    if (data.use_count > 0) {
        return feedback.msgError('该模型已被前台用户使用，无法删除')
    }
    dataVm.value.splice(index, 1)
}

onMounted(async () => {
    nextTick(() => {
        initSortable()
    })
})
</script>
