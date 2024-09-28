<template>
    <div>
        <div>
            <div class="font-medium text-xl mb-4">对话模型限制</div>
            <el-table size="large" :data="formData.model_list.chat_model">
                <el-table-column label="模型名称" prop="name" min-width="150" />
                <el-table-column label="使用上限/天" min-width="150">
                    <template #default="{ row }">
                        <el-input
                            v-model="row.day_limit"
                            placeholder="为空或为0表示不限制"
                            clearable
                            type="number"
                            class="w-[250px]"
                        >
                            <template #append>
                                <span>次</span>
                            </template>
                        </el-input>
                    </template>
                </el-table-column>
                <el-table-column label="会员免费" min-width="150">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div>
            <div class="font-medium text-xl my-4">向量模型限制</div>
            <el-table size="large" :data="formData.model_list.vector_model">
                <el-table-column label="模型名称" prop="name" min-width="150" />
                <el-table-column label="使用上限/天" min-width="150">
                    <template #default="{ row }">
                        <el-input
                            v-model="row.day_limit"
                            placeholder="为空或为0表示不限制"
                            clearable
                            class="w-[250px]"
                        >
                            <template #append>
                                <span>次</span>
                            </template>
                        </el-input>
                    </template>
                </el-table-column>
                <el-table-column label="会员免费" min-width="150">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>
<script setup lang="ts">
import { useVModels } from "@vueuse/core";
import { memberModelLists } from "@/api/marketing/member";
import type { MemberRequest } from "@/api/marketing/member_d";

interface Props {
    modelValue: MemberRequest;
}
const props = defineProps<Props>();
const { query } = useRoute();

const emit = defineEmits(["update:modelValue"]);
const { modelValue: formData } = useVModels(props, emit);

const getData = async () => {
    try {
        const { chat_list, vector_list } = await memberModelLists();
        formData.value.model_list.chat_model = chat_list;
        formData.value.model_list.vector_model = vector_list;
    } catch (error) {
        console.log("获取模型列表失败=>", error);
    }
};

onMounted(() => {
    !query?.id && getData();
});
</script>
