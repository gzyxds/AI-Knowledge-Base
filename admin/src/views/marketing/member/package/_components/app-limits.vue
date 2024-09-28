<template>
    <div>
        <div class="font-medium text-xl mb-4">应用限制</div>
        <el-table size="large" :data="formData.apply_list">
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
</template>
<script setup lang="ts">
import { useVModels } from "@vueuse/core";
import { memberModelLists } from "@/api/marketing/member";
import type { MemberRequest } from "@/api/marketing/member_d";

interface Props {
    modelValue: MemberRequest;
}
const { query } = useRoute();
const props = defineProps<Props>();

const emit = defineEmits(["update:modelValue"]);
const { modelValue: formData } = useVModels(props, emit);

const getData = async () => {
    try {
        const { apply_list } = await memberModelLists();
        formData.value.apply_list = apply_list;
    } catch (error) {
        console.log("获取模型列表失败=>", error);
    }
};

onMounted(() => {
    !query?.id && getData();
});
</script>
