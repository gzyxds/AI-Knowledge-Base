<template>
    <div class="workbench">
        <div class="lg:flex">
            <el-card class="!border-none mb-4 lg:mr-4 flex-2" shadow="never">
                <template #header>
                    <span class="card-title">版本信息 </span>
                </template>
                <div>
                    <div class="flex leading-9">
                        <div class="w-20">网站名称</div>
                        <span> {{ workbenchData.version.name }}</span>
                    </div>
                    <div class="flex leading-9">
                        <div class="w-20">当前版本</div>
                        <span> {{ workbenchData.version.version }}</span>
                    </div>
                    <div class="flex leading-9">
                        <div class="w-20">pc端链接</div>
                        <div>
                            <a :href="workbenchData.version.website" target="_blank">
                                <el-button type="primary" link>{{
                                    workbenchData.version.website
                                }}</el-button>
                            </a>
                        </div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none mb-4 flex-1" shadow="never">
                <template #header>
                    <div>
                        <span class="card-title">今日数据</span>
                        <span class="ml-4 text-xs text-tx-secondary">
                            更新时间：{{ workbenchData.today.time }}
                        </span>
                    </div>
                </template>

                <div class="flex flex-wrap">
                    <div class="w-1/4">
                        <div class="leading-10">今日收入</div>
                        <div class="text-6xl">{{ workbenchData.today.today_amount }}</div>
                        <div class="text-base">
                            <span class="text-[#111111]"
                                >昨日: {{ workbenchData.today.yesterday_amount }}</span
                            >
                            <span
                                class="ml-3 text-[#ff2735]"
                                v-if="workbenchData.today.contrast_amount > 0"
                            >
                                涨:
                                {{ workbenchData.today.contrast_amount }}
                            </span>
                            <span
                                class="ml-3 text-[#389e0d]"
                                v-if="workbenchData.today.contrast_amount < 0"
                            >
                                降:
                                {{ Math.abs(workbenchData.today.contrast_amount) }}
                            </span>
                        </div>
                    </div>
                    <div class="w-1/4">
                        <div class="leading-10">订单数量</div>
                        <div class="text-6xl">{{ workbenchData.today.today_order }}</div>
                        <div class="text-base">
                            <span class="text-[#111111]"
                                >昨日: {{ workbenchData.today.yesterday_order }}</span
                            >
                            <span
                                class="ml-3 text-[#ff2735]"
                                v-if="workbenchData.today.contrast_order > 0"
                            >
                                涨:

                                {{ workbenchData.today.contrast_order }}
                            </span>
                            <span
                                class="ml-3 text-[#389e0d]"
                                v-if="workbenchData.today.contrast_order < 0"
                            >
                                降:
                                {{ Math.abs(workbenchData.today.contrast_order) }}
                            </span>
                        </div>
                    </div>
                    <div class="w-1/4">
                        <div class="leading-10">新增用户</div>
                        <div class="text-6xl">{{ workbenchData.today.today_user }}</div>
                        <div class="text-base">
                            <span class="text-[#111111]"
                                >昨日: {{ workbenchData.today.yesterday_user }}</span
                            >
                            <span
                                class="ml-3 text-[#ff2735]"
                                v-if="workbenchData.today.contrast_user > 0"
                            >
                                涨:
                                {{ workbenchData.today.contrast_user }}
                            </span>
                            <span
                                class="ml-3 text-[#389e0d]"
                                v-if="workbenchData.today.contrast_user < 0"
                            >
                                降:
                                {{ Math.abs(workbenchData.today.contrast_user) }}
                            </span>
                        </div>
                    </div>
                    <div class="w-1/4">
                        <div class="leading-10">新增访问量</div>
                        <div class="text-6xl">{{ workbenchData.today.today_visitor }}</div>
                        <div class="text-base">
                            <span class="text-[#111111]"
                                >昨日: {{ workbenchData.today.yesterday_visitor }}</span
                            >
                            <span
                                class="ml-3 text-[#ff2735]"
                                v-if="workbenchData.today.contrast_visitor > 0"
                            >
                                涨:
                                {{ workbenchData.today.contrast_visitor }}
                            </span>
                            <span
                                class="ml-3 text-[#389e0d]"
                                v-if="workbenchData.today.contrast_visitor < 0"
                            >
                                降:
                                {{ Math.abs(workbenchData.today.contrast_visitor) }}
                            </span>
                        </div>
                    </div>
                </div>
            </el-card>
        </div>
        <div class="function lg:flex">
            <el-card class="flex-1 !border-none lg:mr-4 mb-4" shadow="never">
                <template #header>
                    <span>待办事项</span>
                </template>
                <div class="grid grid-cols-4 gap-4">
                    <router-link
                        :to="{
                            path: getRoutePath('kb.robot/lists')
                        }"
                        class="flex flex-col items-center justify-between"
                    >
                        <div class="text-[24px]">{{ workbenchData.wait?.robot }}</div>
                        <div class="text-[14px] mt-[10px]">智能体总数</div>
                    </router-link>
                    <router-link
                        :to="{
                            path: getRoutePath('kb.know/lists')
                        }"
                        class="flex flex-col items-center justify-between"
                    >
                        <div class="text-[24px]">
                            {{ workbenchData.wait?.know }}
                        </div>
                        <div class="text-[14px] mt-[10px]">知识库总数</div>
                    </router-link>
                    <router-link
                        :to="{
                            path: getRoutePath('kb.robot/lists')
                        }"
                        class="flex flex-col items-center justify-between"
                    >
                        <div class="text-[24px] text-primary">
                            {{ workbenchData.wait?.today_robot }}
                        </div>
                        <div class="text-[14px] mt-[10px]">今日新增智能体</div>
                    </router-link>
                    <router-link
                        :to="{
                            path: getRoutePath('kb.know/lists')
                        }"
                        class="flex flex-col items-center justify-between"
                    >
                        <div class="text-[24px] text-primary">
                            {{ workbenchData.wait?.today_know }}
                        </div>
                        <div class="text-[14px] mt-[10px]">今日新增知识库</div>
                    </router-link>
                </div>
            </el-card>
            <el-card class="flex-1 !border-none mb-4" shadow="never">
                <template #header>
                    <span>常用功能</span>
                </template>
                <div class="flex flex-wrap">
                    <div
                        v-for="item in workbenchData.menu"
                        class="md:w-[20%] w-1/4 flex flex-col items-center"
                        :key="item"
                    >
                        <router-link :to="item.url" class="mb-3 flex flex-col items-center">
                            <image-contain width="40px" height="40px" :src="item?.image" />
                            <div class="mt-2">{{ item.name }}</div>
                        </router-link>
                    </div>
                </div>
            </el-card>
        </div>
        <div class="lg:flex">
            <el-card class="flex-1 !border-none lg:mr-4 mb-4" shadow="never">
                <template #header>
                    <span>销售额趋势图</span>
                </template>
                <div>
                    <v-charts
                        style="height: 350px"
                        :option="workbenchData.businessOption"
                        :autoresize="true"
                    />
                </div>
            </el-card>
            <el-card class="flex-1 !border-none mb-4" shadow="never">
                <template #header>
                    <span>访问量趋势图</span>
                </template>
                <div>
                    <v-charts
                        style="height: 350px"
                        :option="workbenchData.visitorOption"
                        :autoresize="true"
                    />
                </div>
            </el-card>

            <!-- <el-card class="!border-none mb-4" shadow="never">
                <template #header>
                    <span>服务支持</span>
                </template>
                <div>
                    <div v-for="(item, index) in workbenchData.support" :key="index">
                        <div
                            class="flex items-center pb-10 pt-10"
                            :class="{
                                'border-b border-br': index == 0
                            }"
                        >
                            <image-contain
                                :width="120"
                                :height="120"
                                class="flex-none"
                                :src="item.image"
                            />
                            <div class="ml-2">
                                <div>{{ item.title }}</div>
                                <div class="text-tx-regular text-xs mt-4">{{ item.desc }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card> -->
        </div>
    </div>
</template>

<script lang="ts" setup name="workbench">
import { getWorkbench } from '@/api/app'
import { getRoutePath } from '@/router'
import vCharts from 'vue-echarts'

// enum wati {
//     application = '智能体总数',
//     knowledge = '知识库总数'
// }

// 表单数据
const workbenchData: any = reactive({
    version: {
        version: '', // 版本号
        website: '', // 官网
        based: '',
        channel: {
            gitee: '',
            website: ''
        }
    },
    support: [],
    today: {}, // 今日数据
    menu: [], // 常用功能
    visitor: [], // 访问量
    article: [], // 文章阅读量
    wait: {},
    visitorOption: {
        xAxis: {
            type: 'category',
            data: [0]
        },
        yAxis: {
            type: 'value'
        },
        legend: {
            data: ['访问量']
        },
        itemStyle: {
            // 点的颜色。
            color: 'red'
        },
        tooltip: {
            trigger: 'axis'
        },
        series: [
            {
                name: '访问量',
                data: [0],
                type: 'line',
                smooth: true
            }
        ]
    },
    businessOption: {
        xAxis: {
            type: 'category',
            data: [0]
        },
        yAxis: {
            type: 'value'
        },
        legend: {
            data: ['营业额']
        },
        itemStyle: {
            // 点的颜色。
            color: 'red'
        },
        tooltip: {
            trigger: 'axis'
        },
        series: [
            {
                name: '营业额',
                data: [0],
                type: 'line',
                smooth: true
            }
        ]
    }
})

// 获取工作台主页数据
const getData = () => {
    getWorkbench()
        .then((res: any) => {
            workbenchData.version = res.version
            workbenchData.today = res.today
            workbenchData.menu = res.menu
            workbenchData.visitor = res.visitor
            workbenchData.support = res.support
            workbenchData.wait = res.wait
            // 清空echarts 数据
            workbenchData.visitorOption.xAxis.data = []
            workbenchData.visitorOption.series[0].data = []
            workbenchData.businessOption.xAxis.data = []
            workbenchData.businessOption.series[0].data = []

            // 写入从后台拿来的数据
            res.visitor.date.reverse().forEach((item: any) => {
                workbenchData.visitorOption.xAxis.data.push(item)
            })
            res.visitor.list[0].data.reverse().forEach((item: any) => {
                workbenchData.visitorOption.series[0].data.push(item)
            })
            res.sales.date.reverse().forEach((item: any) => {
                workbenchData.businessOption.xAxis.data.push(item)
            })
            res.sales.list[0].data.reverse().forEach((item: any) => {
                workbenchData.businessOption.series[0].data.push(item)
            })
        })
        .catch((err: any) => {
            console.log('err', err)
        })
}

const today_new_user_change = computed(() => {
    return workbenchData.today.today_new_user - workbenchData.today.yesterday_new_user
})
const today_order_num_change = computed(() => {
    return workbenchData.today.today_order_num - workbenchData.today.yesterday_order_num
})
const today_sales_change = computed(() => {
    return workbenchData.today.today_sales - workbenchData.today.yesterday_sales
})
const today_visitor_change = computed(() => {
    return workbenchData.today.today_visitor - workbenchData.today.yesterday_visitor
})

onMounted(() => {
    getData()
})
</script>

<style lang="scss" scoped></style>
