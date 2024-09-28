<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="90px"
            >
                <el-form-item label="模型封面" prop="cover">
                    <material-picker v-model="formData.cover" :limit="1" />
                </el-form-item>
                <el-form-item label="模型名称" prop="title">
                    <el-input
                        class="ls-input"
                        v-model="formData.title"
                        placeholder="不填写则使用默认名称"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="模型文件" prop="model_name">
                    <div class="w-[380px]">
                        <el-select
                            v-model="formData.model_name"
                            placeholder="请选择模型文件"
                            class="w-full"
                        >
                            <el-option
                                v-for="(item, index) in optionsData.loraList"
                                :key="index"
                                :label="item.name"
                                :value="item.name"
                            />
                        </el-select>
                    </div>
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div>
                        <el-input class="ls-input" v-model="formData.sort" />
                        <div class="form-tips">默认为0，数值越大排越前面</div>
                    </div>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-switch
                        v-model="formData.status"
                        :active-value="1"
                        :inactive-value="0"
                    />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from "element-plus";
import { addLora, editLora, getSdLora } from "@/api/ai_draw/draw_lora";
import Popup from "@/components/popup/index.vue";
import { useDictOptions } from "@/hooks/useDictOptions";

const emit = defineEmits(["success", "close"]);
const formRef = shallowRef<FormInstance>();
const popupRef = shallowRef<InstanceType<typeof Popup>>();
const mode = ref("add");
//弹框标题
const popupTitle = computed(() => {
    return mode.value == "edit" ? "编辑微调模型" : "新增微调模型";
});
//表单数据
const formData = reactive({
    id: "",
    title: "",
    model_name: "",
    cover: "",
    sort: 0,
    status: 1,
});
//校验规则
const rules = {
    cover: [
        {
            required: true,
            message: "请上传模型封面",
            trigger: ["blur", "change"],
        },
    ],
    model_name: [
        {
            required: true,
            message: "请选择模型文件",
            trigger: ["blur", "change"],
        },
    ],
};

const { optionsData } = useDictOptions<{ loraList: any[] }>({
    loraList: {
        api: getSdLora,
        transformData(data) {
            return data;
        },
    },
});

//提交
const handleSubmit = async () => {
    await formRef.value?.validate();
    const data = JSON.parse(JSON.stringify(formData));
    if (!data.title) {
        const model = optionsData.loraList.find(
            (item) => item.name == data.model_name
        );
        if (model) {
            data.title = model.alias;
        }
    }
    mode.value == "edit" ? await editLora(data) : await addLora(data);
    popupRef.value?.close();
    emit("success");
};

const handleClose = () => {
    emit("close");
};

const open = (type = "add") => {
    mode.value = type;
    popupRef.value?.open();
};

const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key];
        }
    }
};

defineExpose({
    open,
    setFormData,
});
</script>
