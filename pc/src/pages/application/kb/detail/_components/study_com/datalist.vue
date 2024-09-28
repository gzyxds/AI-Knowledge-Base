<template>
    <div class="p-main flex flex-col">
        <div class="flex justify-between">
            <div class="flex items-center">
                <el-button
                    type="primary"
                    :disabled="knowDetail?.power === 3"
                    @click="$emit('toImport')"
                >
                    导入数据
                </el-button>

                <div
                    class="flex items-center ml-4 cursor-pointer"
                    @click="resetPage"
                >
                    <Icon
                        name="el-icon-RefreshLeft"
                        size="24"
                        color="#666666"
                    ></Icon>
                </div>
            </div>
            <div class="mt-2 md:mt-0">
                <el-input
                    v-model="queryParams.keyword"
                    class="!w-[280px]"
                    placeholder="请输入问题/回答内容关键词进行搜索"
                    clearable
                    @keyup.enter="resetPage"
                />
                <!-- <el-select @change="selectChange" v-model="queryParams.status" class="w-[280px] lg:ml-2 mt-2 md:mt-0">
<el-option label="全部" value />
<el-option label="等待学习" :value="0" />
<el-option label="学习中" :value="1" />
<el-option label="学习失败" :value="3" />
<el-option label="学习成功" :value="2" />
</el-select> -->
                <el-button class="ml-2" type="primary" @click="resetPage">
                    查询
                </el-button>
                <el-button @click="resetParams"> 重置</el-button>
            </div>
        </div>

        <el-table
            v-loading="pager.loading"
            class="mt-4 cursor-pointer flex-1 min-h-0"
            :data="pager.lists"
            size="large"
            row-class-name="h-[70px]"
            @selection-change="handleSelectionChange"
            @row-click="tableItemClick"
        >
            <!-- <el-table-column type="selection" width="55" /> -->
            <!-- <el-table-column label="ID" prop="id" min-width="80" /> -->

            <el-table-column label="名称" prop="name" min-width="250">
                <template #default="{ row, $index }">
                    <div class="flex items-center">
                        <el-image
                            class="w-[22px] h-[22px] flex-none"
                            v-if="row.is_default == 1"
                            :src="byHandImg"
                        ></el-image>
                        <el-image
                            class="w-[22px] h-[22px] flex-none"
                            v-if="row.is_default != 1"
                            :src="getUnitImg(row.name)"
                        ></el-image>
                        <div class="ml-2">
                            <div>{{ row.name }}</div>
                            <template v-if="row.is_qa == 1">
                                <el-tag v-if="row.qa?.status == 0" type="info"
                                    >等待拆分
                                </el-tag>
                                <el-tag
                                    v-if="row.qa?.status == 1"
                                    type="warning"
                                >
                                    拆分中
                                </el-tag>
                                <!-- <el-tag v-if="row.qa?.status == 2"  type="success"> 拆分成功</el-tag> -->
                                <el-tag
                                    v-if="row.qa?.status == 3"
                                    type="danger"
                                    @click.stop="showText(row.qa?.error)"
                                >
                                    拆分失败
                                </el-tag>
                            </template>
                        </div>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="待训练" prop="wait_sum" min-width="150" />
            <el-table-column label="已训练" prop="ok_sum" min-width="150" />
            <el-table-column
                label="数据总量"
                prop="total_sum"
                min-width="150"
            />
            <el-table-column
                label="创建时间"
                prop="create_time"
                min-width="150"
            />
            <el-table-column label="操作" min-width="200" fixed="right">
                <template #default="{ row, $index }">
                    <!-- <el-button
v-if="row.status != 1"
type="primary"
link
@click="showEditQaPopup(row)"
>
修正
</el-button>
<el-button
v-if="row.status == StatusEnum.LEARNING_FAILURES"
type="primary"
link
@click="retry([row.uuid])"
>
重试
</el-button> -->

                    <el-button type="primary" link @click.stop="rename(row.id)">
                        重命名
                    </el-button>
                    <el-button
                        v-if="row.qa?.status == 3"
                        type="primary"
                        link
                        @click.stop="retry(row.id)"
                    >
                        重新拆分
                    </el-button>
                    <el-button
                        type="danger"
                        v-if="$index != 0"
                        link
                        @click.stop="dataDel(row.id)"
                    >
                        删除
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
        <rename-pop
            v-if="popShow"
            ref="renamePopRef"
            @success="getLists"
            @close="popShow = false"
        ></rename-pop>
        <!-- <EditQa
ref="editQaRef"
v-model="editData"
title="编辑内容"
@confirm="handleEdit"
/> -->
    </div>
</template>

<script setup lang="ts">
import { fileDataList, fileDelete, fileRetry } from '@/api/my_database'
import byHandImg from '@/assets/image/kb/byHand.png'
import xlsx from '@/assets/image/kb/xlsx.png'
import csv from '@/assets/image/kb/csv.png'
import doc from '@/assets/image/kb/doc.png'
import pdf from '@/assets/image/kb/pdf.png'
import txt from '@/assets/image/kb/txt.png'
import markdown from '@/assets/image/kb/markdown.png'

import renamePop from './renamePop.vue'


enum StatusEnum {
    ALL = -1,
    WAITING_TO_LEARN = 0,
    LEARNING = 1,
    LEARN_TO_SUCCEED = 2,
    LEARNING_FAILURES = 3
}

const emits = defineEmits(['toImport', 'toItemList'])
const props = defineProps({
    id: {
        type: Number,
        default: 0
    }
})

const knowDetail = inject('knowDetail')
const popShow = ref(false)
const renamePopRef = shallowRef()

const suffixUnitMap: Record<string, string> = {
    doc: doc,
    pdf: pdf,
    txt: txt,
    xlsx: xlsx,
    csv: csv,
    mark: markdown
}

const getUnitImg = computed(() => {
    return (name: string) => {
        // 截取后缀并匹配相近单词的图标
        const suffix: string | undefined = name.split('.').pop()
        for (const nameKey in suffixUnitMap) {
            if (suffix?.includes(nameKey)) {
                return suffixUnitMap[nameKey]
            }
        }
    }
})

const showText = (text: string) => {
    ElMessageBox.alert(`失败原因：${text}`, '拆分失败', {
        customStyle: {
            maxWidth: '400px'
        }
    })
}
const queryParams = ref({
    keyword: '',
    status: '',
    kb_id: props.id
})

//多选数据
const selectData = ref<number[]>([])

const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map((item) => item.uuid)
}


const dataDel = async (fd_id: any) => {
    await feedback.confirm('确定删除？')
    await fileDelete({ fd_id })
    getLists()
}

//跳转至单个文件
const tableItemClick = (row: any) => {
    console.log(row)
    emits('toItemList', row.id, row.name)
}

//重命名
const rename = async (id: number) => {
    popShow.value = true
    await nextTick()
    renamePopRef.value.open(id)
}

const retry = async (id: any) => {
    await feedback.confirm('确定重新拆分？')
    await fileRetry({ kb_id: props.id, fd_id: id })
    getLists()
}

//分页组件
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: fileDataList,
    params: queryParams.value
})

const selectChange = () => {
    resetPage()
}

onMounted(() => {
    getLists()
    setTimeout(() => {
        console.log(props.id)
    }, 1000)
})
</script>

<style scoped lang="scss"></style>