<template>
    <div>
        <div class="font-medium text-xl mb-4">会员权益</div>
        <el-table
            size="large"
            row-key="id"
            ref="benefitsTableRef"
            :data="formData.benefits_list"
        >
            <el-table-column width="50">
                <template #default>
                    <div class="move-icon cursor-move">
                        <Icon name="el-icon-Rank" />
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="图标" min-width="80">
                <template #default="{ row }">
                    <div class="py-[8px]">
                        <materialPicker size="60px" v-model="row.image" />
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="名称" min-width="130">
                <template #default="{ row }">
                    <el-input
                        v-model="row.name"
                        placeholder="请输入"
                        clearable
                        class="w-[80%]"
                    >
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="用量" min-width="130">
                <template #default="{ row }">
                    <el-input
                        v-model="row.describe"
                        placeholder="请输入"
                        clearable
                        class="w-[80%]"
                    >
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="状态" min-width="90">
                <template #default="{ row }">
                    <el-switch
                        v-model="row.status"
                        :active-value="1"
                        :inactive-value="0"
                    />
                </template>
            </el-table-column>
            <el-table-column label="操作" min-width="130" fixed="right">
                <template #default="{ row, $index }">
                    <ElButton type="danger" link @click="benefitsDel($index)">
                        删除
                    </ElButton>
                </template>
            </el-table-column>
        </el-table>
        <el-button type="primary" class="mt-4" @click="benefitsAdd">添加</el-button>
    </div>
</template>
<script setup lang="ts">
import {useVModels} from '@vueuse/core';
import Sortable from 'sortablejs'
import type { ElTable } from 'element-plus'
import {memberModelLists} from '@/api/marketing/member';
import type {MemberRequest} from '@/api/marketing/member_d';

interface Props { modelValue: MemberRequest }
const props = defineProps<Props>()

const emit = defineEmits(['update:modelValue'])
const { modelValue: formData } = useVModels(props, emit)

const benefitsTableRef = ref<InstanceType<typeof ElTable>>()

// 添加权益
const benefitsAdd = () => {
    formData.value.benefits_list.push({
        id: new Date().getTime(),
        name: '',
        image: '',
        describe: '',
        status: 1
    })
}

// 删除权益
const benefitsDel = (index:number) => {
    formData.value.benefits_list.splice(index, 1)
}


// —————————————————————————————初始化拖拽排序的插件
const initSortable = () => {
    const el = benefitsTableRef.value?.$el.querySelector('.el-table__body tbody')
    Sortable.create(el, {
        animation: 150,
        handle: '.move-icon',
        onEnd: ({ newIndex, oldIndex }: any) => {
            console.log(newIndex, oldIndex)
            const arr = formData.value?.benefits_list
            const currRow = arr.splice(oldIndex, 1)[0]
            arr.splice(newIndex, 0, currRow)
            formData.value.benefits_list = arr
        }
    })
}
onMounted(async () => { await nextTick(); initSortable() })
</script>