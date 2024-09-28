<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="headerTitle" @back="$router.back()" />
        </el-card>

        <el-form
            class="mt-4"
            ref="formRef"
            :model="formData"
            label-width="120px"
            :rules="formRules"
        >
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">基础配置</div>
                <el-form-item label="图标" prop="logo">
                    <div>
                        <material-picker v-model="formData.logo" :limit="1" />
                    </div>
                </el-form-item>
                <el-form-item label="AI通道" prop="channel">
                    <div class="w-[460px]">
                        <el-select
                            class="w-full"
                            v-model="formData.channel"
                            :disabled="!!currentId"
                            placeholder="请选择AI通道"
                            @change="changeChannel"
                        >
                            <el-option
                                v-for="(item, key) in currentChannels"
                                :value="key"
                                :label="item.name"
                                :key="key"
                            />
                        </el-select>
                        <div v-if="selectChannels?.website" class="form-tips">
                            开通网址：{{ selectChannels?.website }}
                            <a class="text-primary" target="_blank" :href="selectChannels?.website"
                                >前往开通</a
                            >
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="AI名称" prop="name">
                    <div class="w-[460px]">
                        <el-input
                            v-model.trim="formData.name"
                            placeholder="请输入AI名称"
                            maxlength="30"
                            show-word-limit
                        />
                    </div>
                </el-form-item>
                <el-form-item label="描述" prop="remarks">
                    <div class="w-[460px]">
                        <el-input
                            v-model.trim="formData.remarks"
                            placeholder="请用一句简短的话描述一下"
                            type="textarea"
                            :rows="4"
                        />
                    </div>
                </el-form-item>

                <el-form-item
                    v-for="item in selectChannels?.configs"
                    :label="item.name"
                    :prop="item.key"
                    :key="item.key"
                    :rules="item.require ? [
                        {
                            required: item.require,
                            trigger: 'change',
                            validator(rule: any, value: any, callback: any) {
                                if(item.key && !value) {
                                    callback(new Error(item.placeholder ))
                                }else {
                                    callback()
                                }
                            }
                        }
                    ] : []"
                >
                    <div class="w-[460px]">
                        <template v-if="item.type === 'group'">
                            <div class="flex flex-wrap">
                                <div
                                    class="w-[50%] pr-[20px] pb-[20px]"
                                    v-for="conf in item.config"
                                    :key="conf.key"
                                >
                                    <div class="flex items-center text-tx-regular text-xs">
                                        <span class="mr-[4px] mt-[2px]">{{ conf.name }}</span>
                                        <el-tooltip
                                            class="box-item"
                                            effect="dark"
                                            :content="conf.tips"
                                            placement="top"
                                        >
                                            <el-icon size="16px"><QuestionFilled /></el-icon>
                                        </el-tooltip>
                                    </div>
                                    <template v-if="conf.type === 'slider'">
                                        <el-slider
                                            v-model="formData.configs[conf.key]"
                                            :min="conf.range[0]"
                                            :max="conf.range[1]"
                                            :step="conf.step"
                                        />
                                    </template>
                                    <template v-if="conf.type === 'switch'">
                                        <el-switch v-model="formData.configs[conf.key]" />
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-if="item.type === 'input'">
                            <el-input
                                v-model.trim="formData.configs[item.key]"
                                :placeholder="item.placeholder || '请输入'"
                            />
                            <div class="form-tips">{{ item.tips }}</div>
                        </template>
                        <template v-if="item.type === 'textarea'">
                            <el-input
                                v-model.trim="formData.configs[item.key]"
                                :placeholder="item.placeholder || '请输入'"
                                type="textarea"
                                :rows="4"
                            />
                            <div class="form-tips">{{ item.tips }}</div>
                        </template>
                    </div>
                </el-form-item>
                <el-form-item label="是否启用" prop="is_enable">
                    <el-switch v-model="formData.is_enable" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">
                    模型配置
                    <el-tooltip
                        content="模型可自定义添加，点击下方的新增按钮"
                        placement="top"
                        effect="light"
                        :teleported="true"
                    >
                        <span class="inline-flex text-tx-secondary">
                            <Icon name="el-icon-QuestionFilled" />
                        </span>
                    </el-tooltip>
                </div>
                <div class="flex-1 min-w-0 max-w-[1000px]">
                    <template v-if="Number(type) == 1">
                        <div class="">
                            <el-table
                                ref="tableRef"
                                size="large"
                                class="mt-4"
                                row-key="id"
                                :data="formData.models"
                            >
                                <el-table-column width="50">
                                    <template #default>
                                        <div class="move-icon cursor-move">
                                            <Icon name="el-icon-Rank" />
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column label="模型名称" min-width="120">
                                    <template #default="{ row }">
                                        <el-autocomplete
                                            class="w-full"
                                            v-model.trim="row.name"
                                            placeholder="请输入AI模型"
                                            clearable
                                            :fetch-suggestions="querySearch"
                                        />
                                    </template>
                                </el-table-column>
                                <el-table-column label="自定义名称" min-width="120">
                                    <template #default="{ row }">
                                        <ElInput
                                            v-model="row.alias"
                                            placeholder="请输入自定义名称"
                                        />
                                    </template>
                                </el-table-column>
                                <el-table-column label="1000字符消耗电力值" min-width="120">
                                    <template #default="{ row }">
                                        <ElInput
                                            v-model="row.price"
                                            type="number"
                                            placeholder="为空不消耗电力值"
                                        />
                                    </template>
                                </el-table-column>

                                <el-table-column label="状态" min-width="100">
                                    <template #default="{ row }">
                                        <el-switch
                                            v-model="row.status"
                                            :before-change="() => statusChange(row)"
                                            :active-value="1"
                                            :inactive-value="0"
                                        />
                                    </template>
                                </el-table-column>

                                <el-table-column label="操作" width="120" fixed="right">
                                    <template #default="{ row, $index }">
                                        <el-button
                                            type="danger"
                                            link
                                            @click="handleDelete($index, row.id)"
                                        >
                                            删除
                                        </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>
                        <div class="flex mt-4">
                            <el-button type="primary" @click="handleAdd">+ 新增</el-button>
                        </div>
                    </template>
                    <template v-else>
                        <el-form ref="formRef1" :model="modelConfig" label-width="120px">
                            <el-form-item
                                label="AI模型"
                                :rules="[
                                    {
                                        required: true,
                                        message: '请输入AI模型'
                                    }
                                ]"
                                prop="name"
                            >
                                <div class="w-[460px]">
                                    <el-autocomplete
                                        class="w-full"
                                        v-model.trim="modelConfig.name"
                                        placeholder="请输入AI模型"
                                        clearable
                                        :fetch-suggestions="querySearch"
                                    />
                                </div>
                            </el-form-item>
                            <el-form-item label="自定义别名" prop="alias">
                                <div class="w-[460px]">
                                    <el-input
                                        v-model.trim="modelConfig.alias"
                                        placeholder="请输入自定义别名"
                                        maxlength="30"
                                        show-word-limit
                                    />
                                    <div class="form-tips">
                                        填写前端就显示该字段，不填写默认显示AI模型名称
                                    </div>
                                </div>
                            </el-form-item>
                            <el-form-item
                                label="消耗电力值"
                                :rules="[
                                    {
                                        required: true,
                                        message: '请输入消耗电力值'
                                    }
                                ]"
                                prop="price"
                            >
                                <div class="w-[460px]">
                                    <el-input v-model.trim="modelConfig.price" type="number" />
                                    <div class="form-tips">填写0表示不消耗电力值</div>
                                </div>
                            </el-form-item>
                        </el-form>
                    </template>
                </div>
            </el-card>
        </el-form>
    </div>
</template>

<script setup lang="ts">
import { getRoutePath } from '@/router'
import { useVModel } from '@vueuse/core'
import type { PropType } from 'vue'
import { getAiModelChannels } from '@/api/ai_setting/model'
import { computed } from 'vue'
import Sortable from 'sortablejs'
import feedback from '@/utils/feedback'
const props = defineProps({
    modelValue: {
        type: Object as PropType<Record<string, any>>,
        required: true
    },
    type: {
        type: String
    },
    currentId: {
        type: [Number, String]
    },
    headerTitle: {
        type: String
    }
})

const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()

const formRef = shallowRef()
const formData = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})
const formRef1 = shallowRef()
const modelConfig = ref({
    alias: '',
    name: '',
    status: 1,
    price: ''
})

watch(
    modelConfig,
    (value) => {
        if (Number(props.type) == 1) return
        formData.value.models = [value]
    },
    {
        deep: true
    }
)

watch(
    formData,
    (value) => {
        const [item] = value.models
        if (item) {
            modelConfig.value = item
        }
    },
    {
        immediate: true
    }
)

const formRules = {
    logo: [
        {
            required: true,
            message: '请选择图标'
        }
    ],
    channel: [
        {
            required: true,
            message: '请选择AI通道'
        }
    ],
    name: [
        {
            required: true,
            message: '请输入AI名称'
        }
    ]
}

const tableRef = shallowRef()
const initSortable = () => {
    const el = tableRef.value.$el.querySelector('.el-table__body tbody')
    Sortable.create(el, {
        animation: 150,
        handle: '.move-icon',
        onEnd: ({ newIndex, oldIndex }: any) => {
            const arr = formData.value.models
            const currRow = arr.splice(oldIndex, 1)[0]
            arr.splice(newIndex, 0, currRow)
            formData.value.models = []
            nextTick(() => {
                formData.value.models = arr
                formData.value.models.forEach((item: any, index: number) => {
                    item.sort = index
                })
            })
        }
    })
}

const handleAdd = () => {
    formData.value.models.push({
        name: '',
        alias: '',
        status: 1,
        price: '',
        sort: formData.value.models.length
    })
}

const statusChange = (row: any) => {
    if (row.id && row.status == 1) {
        return feedback.confirm(
            '该模型可能已经被使用，禁用后已经选择该模型的用户将无法正常使用，请谨慎操作！'
        )
    }
    return true
}
const handleDelete = async (index: number, id: number) => {
    if (id) {
        await feedback.confirm(
            '该模型可能已经被使用，删除后已经选择该模型的用户将无法正常使用，请谨慎操作！'
        )
    }

    formData.value.models.splice(index, 1)
}
const aiChannels = ref<any>({
    chatModels: {},
    exampleModels: {
        chat: [],
        vector: []
    },
    vectorModels: {}
})
const currentChannels = computed(() => {
    if (Number(props.type) === 1) return aiChannels.value.chatModels
    else return aiChannels.value.vectorModels
})

const currentExample = computed(() => {
    if (Number(props.type) === 1) return aiChannels.value.exampleModels.chat
    else return aiChannels.value.exampleModels.vector
})

const querySearch = (queryString: string, cb: any) => {
    const results = queryString
        ? currentExample.value.filter((item: string) =>
              item.toLowerCase().includes(queryString.toLowerCase())
          )
        : currentExample.value
    cb(results.map((item: string) => ({ value: item })))
}

const selectChannels = computed(() => {
    return currentChannels.value[formData.value.channel]
})
const changeChannel = () => {
    const getDefaultConfig = (config: any[]) => {
        const result: any = {}
        config.reduce((prev, item) => {
            if (item.type == 'group') {
                Object.assign(prev, getDefaultConfig(item.config))
            } else {
                prev[item.key] = item.default
            }
            return prev
        }, result)
        return result
    }
    const config = getDefaultConfig(selectChannels.value.configs)
    formData.value.configs = config
}

const getChannels = async () => {
    aiChannels.value = await getAiModelChannels()
}

getChannels()

onMounted(() => {
    setTimeout(() => {
        Number(props.type) == 1 && initSortable()
    }, 100)
})

const validate = () => {
    return Promise.all([formRef.value?.validate(), formRef1.value?.validate()])
}

defineExpose({
    validate
})
</script>

<style scoped lang="scss"></style>
