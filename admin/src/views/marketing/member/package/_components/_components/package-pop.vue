<template>
    <div class="package-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleConfirm"
            @close="emit('close')"
        >
            <el-form
                ref="formRef"
                :rules="formRules"
                :model="formData"
                label-width="90px"
            >
                <el-form-item label="会员时长" prop="duration">
                    <el-input
                        v-model="formData.duration"
                        placeholder="请输入整数"
                        clearable
                        :disabled="formData.duration_type === 3"
                        class="w-[360px]"
                    >
                        <template #append>
                            <el-select v-model="formData.duration_type" class="w-[80px]">
                                <el-option :value="1" label="天"></el-option>
                                <el-option :value="2" label="个月"></el-option>
                                <el-option :value="3" label="永久"></el-option>
                            </el-select>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item label="销售价格" prop="sell_price">
                    <el-input
                        v-model="formData.sell_price"
                        class="w-[360px]"
                        placeholder="请输入销售价格，支持两位小数点"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="划线价格">
                    <el-input
                        v-model="formData.lineation_price"
                        clearable
                        class="w-[360px]"
                        placeholder="请输入"
                    />
                </el-form-item>
                <el-form-item label="会员标签">
                    <div>
                        <el-input
                            v-model="formData.tags"
                            clearable
                            class="w-[360px]"
                            placeholder="请输入"
                        />
                        <div class="form-tips">填写就在前台显示，不填写不显示</div>
                    </div>
                </el-form-item>
                <el-form-item label="开启赠送">
                    <div class="w-80">
                        <el-switch
                            v-model="formData.is_give"
                            :inactive-value="0"
                            :active-value="1"
                        />
                    </div>
                </el-form-item>
                <template v-if="formData.is_give">
                    <el-form-item label="赠送电力值">
                        <div>
                            <el-input
                                v-model="formData.give_balance"
                                clearable
                                class="w-[360px]"
                                placeholder="请输入"
                            />
                        </div>
                    </el-form-item>
                    <el-form-item label="赠送智能体">
                        <div>
                            <el-input
                                v-model="formData.give_robot"
                                clearable
                                class="w-[360px]"
                                placeholder="请输入"
                            />
                        </div>
                    </el-form-item>
                </template>
                <el-form-item label="是否上架">
                    <div class="w-80">
                        <el-switch
                            v-model="formData.status"
                            :inactive-value="0"
                            :active-value="1"
                        />
                    </div>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup>
import { ref, reactive, shallowRef, defineEmits } from 'vue';
import type { FormInstance, FormRules } from 'element-plus';
import type { PriceList } from '@/api/marketing/member_d';
import Popup from '@/components/popup/index.vue';

const emit = defineEmits(['success', 'close']);
// 表单ref
const formRef = shallowRef<FormInstance>();
// 弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>();
// 添加 / 编辑
const mode = ref<string>('add')
// 弹框标题
const popupTitle =computed<string>(() => mode.value === 'add' ? '添加套餐' : '编辑套餐');

// 表单数据
const formData = reactive<PriceList>({
    duration: '',
    duration_type: 1,
    sell_price: '',
    lineation_price: '',
    tags: '',
    status: 1,
    id: 0,
    is_give: 1,
    is_recommend: 0,
    give_balance: '',
    give_robot: ''
});

// 表单校验规则
const formRules = reactive<FormRules>({
    duration: [
        {
            required: true,
            validator: (rule, value, callback) => {
                if (formData.duration_type === 3) {
                    return callback()
                }
                if (!value) {
                    return callback(new Error('请输入会员时长'));
                }
                callback();
            }
        }
    ],
    sell_price:  [
        {
            required: true,
            message: '请输入销售价格',
            trigger: ['blur']
        }
    ]
})

// 提交表单
const handleConfirm = async () => {
    try {
        await formRef.value?.validate();
        emit('success', {
            type: mode.value,
            row: unref(formData)
        });
        popupRef.value?.close();
    } catch (error) {
        console.log('保存套餐失败=>', error)
    }
};

// 打开弹框
const open = (type: string, value: PriceList) => {
    // 初始化数据
    mode.value = type
    if (type == 'edit') {
        Reflect.ownKeys(unref(formData)).forEach((item) => {
            formData[item as keyof PriceList] = value[item as keyof PriceList];
        });
    }
    popupRef.value?.open();
};

defineExpose({ open })
</script>