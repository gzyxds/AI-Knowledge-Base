<template>
    <div v-loading="loading">
        <el-card class="!border-none" shadow="never">
            <el-page-header
                :content="$route.query.id as string ? '编辑模型' : '新增模型'"
                @back="$router.back()"
            />
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="120px"
            >
                <el-form-item label="模型标识" prop="model_name">
                    <div class="w-[380px]">
                        <el-select
                            v-model="formData.model_name"
                            placeholder="请选择模型标识"
                            class="w-full"
                        >
                            <el-option
                                v-for="(item, index) in sdModelList"
                                :key="index"
                                :label="item.title"
                                :value="item.model_name"
                            />
                        </el-select>
                    </div>
                </el-form-item>
                <el-form-item label="模型封面" prop="cover">
                    <material-picker v-model="formData.cover" :limit="1" />
                </el-form-item>
                <el-form-item label="自定义名称" prop="title">
                    <div class="w-[380px]">
                        <el-input
                            placeholder="不填写则显示系统默认名称"
                            v-model="formData.title"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="所属分类" prop="category_id">
                    <div class="flex items-center gap-2 flex-wrap w-full">
                        <div class="w-[380px]">
                            <el-select
                                v-model="formData.category_id"
                                placeholder="请选择所属分类"
                            >
                                <el-option
                                    v-for="(
                                        item, index
                                    ) in optionsData.categoryList"
                                    :key="index"
                                    :label="item.name"
                                    :value="item.id"
                                />
                            </el-select>
                        </div>
                        <div class="flex items-center">
                            <el-button type="primary" link>
                                <router-link
                                    to="/ai_application/sd/model-category"
                                    >新建分类</router-link
                                >
                            </el-button>
                            <el-divider direction="vertical"></el-divider>
                            <el-button type="primary" link @click="refresh">
                                刷新
                            </el-button>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div>
                        <el-input-number v-model="formData.sort" />
                        <div class="form-tips">默认为0，数值越大越排前面</div>
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
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <div class="flex items-center gap-4 mb-4">
                <div>关联微调模型</div>
                <div class="flex items-center">
                    <el-button type="primary" link>
                        <router-link to="/ai_application/sd/lora"
                            >新建微调模型</router-link
                        >
                    </el-button>
                    <el-divider direction="vertical"></el-divider>
                    <el-button type="primary" link @click="refresh">
                        刷新
                    </el-button>
                    <el-button type="primary" link @click="selectLora('all')">
                        {{ selectText }}
                    </el-button>
                </div>
            </div>
            <div
                class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-2"
            >
                <div
                    v-for="(item, index) in loraModelList"
                    :key="item.id"
                    class="flex flex-col gap-1"
                    @click="selectLora(index)"
                >
                    <div class="relative rounded-sm overflow-hidden">
                        <aspect-ratio
                            :src="item.cover"
                            :ratio="[1, 1]"
                        ></aspect-ratio>
                        <div
                            class="absolute top-0 left-0 bg-[rgba(0,0,0,0.4)] w-full h-full flex justify-center items-center transition-opacity opacity-0"
                            :class="{
                                'opacity-100': item.checked,
                            }"
                        >
                            <el-icon :size="20"
                                ><SuccessFilled color="#fff"
                            /></el-icon>
                        </div>
                    </div>
                    <div class="truncate text-center select-none">
                        {{ item.title }}
                    </div>
                </div>
            </div>
        </el-card>
        <footer-btns>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup>
import { ElMessage, type FormInstance, type FormRules } from "element-plus";
import {
    getSdModel,
    addModel,
    editModel,
    getModelDetail,
} from "@/api/ai_draw/draw_model";
import { getLoraListAll } from "@/api/ai_draw/draw_lora";
import { getModelCategoryList } from "@/api/ai_draw/draw_model_category";
import { getDeawConfig } from "@/api/ai_draw/draw_setting";
import { useDictOptions } from "@/hooks/useDictOptions";
import AspectRatio from "@/components/aspect-ratio/index.vue";

interface LoraModelItem {
    id: number;
    cover: string;
    title: string;
    model_name: string;
    sort: number;
    status: number;
    create_time: string;
    checked: boolean;
}
const route = useRoute();
const router = useRouter();
const formRef = shallowRef<FormInstance>();
const loading = ref(false);

const formData = reactive<{
    model_name: string;
    title: string;
    category_id: string;
    sort: number;
    status: number;
    cover: string;
    loras: Record<string, any>[];
}>({
    model_name: "", // 模型标识符
    title: "", //模型名称
    category_id: "", // 分类id
    sort: 1, // 排序
    status: 1, // 状态
    cover: "", // 模型封面
    loras: [], // 微调模型
});
const sdModelList = ref<{ model_name: string; title: string }[]>([]);
const loraModelList = ref<LoraModelItem[]>([]);

const selectText = computed(() => {
    return loraModelList.value.every((item) => item.checked)
        ? "取消全选"
        : "全选";
});

//表单校验规则
const rules: FormRules = {
    model_name: [
        {
            required: true,
            message: "请输入模型描述",
        },
    ],
    category_id: [
        {
            required: true,
            message: "请选择所属类目",
        },
    ],
    cover: [
        {
            required: true,
            message: "请选择模型图标",
        },
    ],
};

const { optionsData, refresh } = useDictOptions<{ categoryList: any[] }>({
    categoryList: {
        api: getModelCategoryList,
        transformData(data) {
            return data;
        },
    },
});

/**
 * 获取微调模型
 */
const getLora = async () => {
    let res: LoraModelItem[] = await getLoraListAll({});
    const loras = formData.loras.map((item) => item.id);
    loraModelList.value = res.map((item: LoraModelItem) => {
        return {
            ...item,
            checked: loras.includes(item.id),
        };
    });
};

/**
 * 选中微调模型
 * @param index 索引
 */
const selectLora = (index: number | "all") => {
    if (index === "all") {
        loraModelList.value.forEach((item: LoraModelItem) => {
            item.checked = !item.checked;
        });
    } else {
        loraModelList.value[index].checked =
            !loraModelList.value[index].checked;
    }
};

/**
 * 提交表单
 */
const handleSave = async () => {
    const loraSelectedlist = loraModelList.value
        .filter((item: LoraModelItem) => item.checked)
        .map((item: LoraModelItem) => item.id);
    await formRef.value?.validate();
    const data = JSON.parse(JSON.stringify(formData));
    if (!data.title) {
        const sdModel = sdModelList.value.find(
            (item) => item.model_name === data.model_name
        );
        if (sdModel) {
            data.title = sdModel.title;
        }
    }
    data.loras = loraSelectedlist;
    route.query.id ? await editModel(data) : await addModel(data);
    router.push("/ai_application/sd/model");
};

/**
 * 获取模型详情
 */
const getDetails = async () => {
    loading.value = true;
    try {
        const data = await getModelDetail({
            id: route.query.id,
        });
        Object.assign(formData, data);
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    console.log(123);
    const res = await getDeawConfig();
    if (res.length < 1 || res.proxy_url === "") {
        return ElMessage.error({
            message: "请先配置SD绘画接口地址",
            onClose: () => {
                router.back();
            },
        });
    }
    console.log(res);
    route.query.id && getDetails();
    sdModelList.value = await getSdModel({});
    getLora();
});
</script>
