<template>
  <div class="h-full flex">
    <InfoMenu
      v-model="currenTab"
      :title="name"
      back-path="/application/layout/digital"
      :menu-list="tabList"
    />
    <div class="h-full flex-1 min-w-0 py-[16px] pr-[16px]">
      <div class="h-full flex flex-col bg-body rounded-2xl">
        <div class="flex-1 min-h-0">
          <ElScrollbar>
            <div class="lg:flex">
              <div class="flex-1 min-w-0">
                <el-form
                  class="p-4"
                  ref="formRef"
                  :model="formData"
                  label-width="140px"
                  :rules="formRules"
                >
                  <el-form-item label="形象名称" prop="name">
                    <div class="w-[420px]">
                      <el-input
                        v-model="formData.name"
                        placeholder="请输入形象名称"
                        clearable
                      />
                    </div>
                  </el-form-item>
                  <el-form-item label="形象头像" prop="avatar">
                    <div>
                      <div>
                        <UploadImg v-model="formData.avatar" />
                      </div>
                      <div class="form-tips">建议尺寸：50*50px</div>
                    </div>
                  </el-form-item>
                  <el-form-item label="形象封面" prop="image">
                    <div>
                      <div>
                        <UploadImg v-model="formData.image" />
                      </div>
                      <div class="form-tips">建议尺寸：280px×187px</div>
                    </div>
                  </el-form-item>
                  <el-form-item label="宽屏人物待机视频" prop="wide_stay_video">
                    <div>
                      <div>
                        <UploadVideo v-model="formData.wide_stay_video" />
                      </div>
                      <div class="form-tips">
                        格式为MP4，大小不能超过20M
                        <span
                          class="text-primary cursor-pointer"
                          @click="
                            handleAddVideo(
                              'wide_stay_video',
                              'wide_stay_video.mp4'
                            )
                          "
                        >
                          使用默认视频
                        </span>
                        <!-- <el-popover
                          placement="top-start"
                          width="auto"
                          trigger="hover"
                        >
                          <template #reference>
                            <span class="text-primary"> 示例 </span>
                          </template>

                          <img
                            src="@/assets/image/digital_example1.png"
                            alt=""
                            class="w-[270px] h-[170px]"
                          />
                        </el-popover> -->
                      </div>
                    </div>
                  </el-form-item>
                  <el-form-item label="宽屏人物说话视频" prop="wide_talk_video">
                    <div>
                      <div>
                        <UploadVideo v-model="formData.wide_talk_video" />
                      </div>
                      <div class="form-tips">
                        格式为MP4，大小不能超过20M
                        <span
                          class="text-primary cursor-pointer"
                          @click="
                            handleAddVideo(
                              'wide_talk_video',
                              'wide_talk_video.mp4'
                            )
                          "
                        >
                          使用默认视频
                        </span>
                        <!-- <el-popover
                          placement="top-start"
                          width="auto"
                          trigger="hover"
                        >
                          <template #reference>
                            <span class="text-primary"> 示例 </span>
                          </template>

                          <img
                            src="@/assets/image/digital_example1.png"
                            alt=""
                            class="w-[270px] h-[170px]"
                          />
                        </el-popover> -->
                      </div>
                    </div>
                  </el-form-item>
                  <el-form-item
                    label="竖屏人物待机视频"
                    prop="vertical_stay_video"
                  >
                    <div>
                      <div>
                        <UploadVideo v-model="formData.vertical_stay_video" />
                      </div>
                      <div class="form-tips">
                        格式为MP4，大小不能超过20M
                        <span
                          class="text-primary cursor-pointer"
                          @click="
                            handleAddVideo(
                              'vertical_stay_video',
                              'vertical_stay_video.mp4'
                            )
                          "
                        >
                          使用默认视频
                        </span>
                        <!-- <el-popover
                          placement="top-start"
                          width="auto"
                          trigger="hover"
                        >
                          <template #reference>
                            <span class="text-primary"> 示例 </span>
                          </template>

                          <img
                            src="@/assets/image/digital_example2.png"
                            alt=""
                            class="w-[270px] h-[350px]"
                          />
                        </el-popover> -->
                      </div>
                    </div>
                  </el-form-item>
                  <el-form-item
                    label="竖屏人物说话视频"
                    prop="vertical_talk_video"
                  >
                    <div>
                      <div>
                        <UploadVideo v-model="formData.vertical_talk_video" />
                      </div>
                      <div class="form-tips">
                        格式为MP4，大小不能超过20M
                        <span
                          class="text-primary cursor-pointer"
                          @click="
                            handleAddVideo(
                              'vertical_talk_video',
                              'vertical_talk_video.mp4'
                            )
                          "
                        >
                          使用默认视频
                        </span>
                        <!-- <el-popover
                          placement="top-start"
                          width="auto"
                          trigger="hover"
                        >
                          <template #reference>
                            <span class="text-primary"> 示例 </span>
                          </template>

                          <img
                            src="@/assets/image/digital_example2.png"
                            alt=""
                            class="w-[270px] h-[350px]"
                          />
                        </el-popover> -->
                      </div>
                    </div>
                  </el-form-item>
                  <el-form-item label="配音角色" prop="dubbing">
                    <div class="flex flex-1">
                      <DubPicker v-model="formData.dubbing" />
                    </div>
                  </el-form-item>
                  <el-form-item label="自定义闲时时间" prop="idle_time">
                    <div class="w-[420px]">
                      <el-input
                        v-model="formData.idle_time"
                        placeholder="请输入自定义闲时时间"
                        type="number"
                      >
                        <template #append>秒</template>
                      </el-input>
                      <div class="form-tips">
                        例如：选择5s，每隔5秒就会有一个回复内容，内容是在闲时回复内容的文案
                      </div>
                    </div>
                  </el-form-item>
                  <el-form-item label="闲时回复内容" prop="idle_reply">
                    <div class="w-[420px]">
                      <el-input
                        v-model="formData.idle_reply"
                        placeholder="请输入闲时回复内容"
                        type="textarea"
                        :rows="4"
                        clearable
                      />
                      <div class="form-tips">
                        根据自定义闲时时间段设置后形象回复的内容
                      </div>
                    </div>
                  </el-form-item>
                </el-form>
              </div>
            </div>
          </ElScrollbar>
        </div>
        <div class="flex p-4 items-center justify-center">
          <ElButton type="primary" @click="handelSubmit">保存</ElButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import {
  postDigital,
  getDubbingList,
  getDigitalDetail,
  putDigital
} from '@/api/digital'
import { baseUrl } from '@/config'
const router = useRouter()
const route = useRoute()
const id = computed(() => route.query.id)
const name = ref('')
const getData = async () => {
  if (id.value) {
    const data = await getDigitalDetail({ id: id.value })
    name.value = data.name
    return data
  } else {
    return Promise.reject()
  }
}

const currenTab = ref('edit')

const tabList = [
  {
    name: '形象设置',
    icon: 'el-icon-Setting',
    key: 'edit'
  }
]
const { data: formData } = await useAsyncData(() => getData(), {
  default() {
    return {
      name: '',
      avatar: '',
      image: '',
      wide_stay_video: '',
      wide_talk_video: '',
      vertical_stay_video: '',
      vertical_talk_video: '',
      channel: '',
      dubbing: '',
      idle_time: 10,
      idle_reply: ''
    }
  },
  lazy: true
})

const formRef = shallowRef<FormInstance>()
const formRules = shallowReactive<FormRules>({
  name: [
    {
      required: true,
      message: '请输入形象名称'
    }
  ],
  avatar: [
    {
      required: true,
      type: 'string',
      message: '请选择形象头像'
    }
  ],
  image: [
    {
      required: true,
      type: 'string',
      message: '请选择形象封面'
    }
  ],
  wide_stay_video: [
    {
      required: true,
      type: 'string',
      message: '请选择宽屏人物待机视频'
    }
  ],
  wide_talk_video: [
    {
      required: true,
      type: 'string',
      message: '请选择宽屏人物说话视频'
    }
  ],
  vertical_stay_video: [
    {
      required: true,
      type: 'string',
      message: '请选择竖屏人物待机视频'
    }
  ],
  vertical_talk_video: [
    {
      required: true,
      type: 'string',
      message: '请选择竖屏人物说话视频'
    }
  ],
  dubbing: [
    {
      required: true,
      message: '请选择配音角色'
    }
  ],
  idle_time: [
    {
      required: true,
      message: '请输入自定义闲时时间'
    }
  ],
  idle_reply: [
    {
      required: true,
      type: 'string',
      message: '请输入闲时回复内容'
    }
  ]
})

const handleAddVideo = (field: string, value: string) => {
  formData.value[field] = baseUrl + '/resource/digital/' + value
}

const handelSubmit = async () => {
  await formRef.value?.validate()
  formData.value.idle_time = Number(formData.value.idle_time)
  const request = id.value
    ? putDigital(formData.value)
    : postDigital(formData.value)
  await request
  setTimeout(() => {
    router.replace({
      path: '/application/layout/digital'
    })
  }, 1000)
}

definePageMeta({
  hiddenFooter: true,
  parentPath: '/application/layout/robot',
  activePath: '/application/layout/digital',
  title: '虚拟形象'
})
</script>

<style lang="scss" scoped></style>
