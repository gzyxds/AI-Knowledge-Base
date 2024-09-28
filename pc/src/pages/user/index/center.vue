<template>
    <div class="w-full h-full bg-body rounded-[12px]">
        <ElScrollbar>
            <div class="p-[20px]">
                <div class="title font-medium text-xl">个人信息</div>
                <div class="mt-[30px]">
                    <el-form label-width="90px" label-position="left">
                        <el-form-item label="" label-position="right">
                            <template #label>
                                <div class="w-[55px] text-right">头像</div>
                            </template>
                            <div class="">
                                <CropperUpload @change="setUserInfo($event, 'avatar')">
                                    <!-- <Icon class="ml-2" name="el-icon-EditPen" /> -->
                                    <!-- <span class="ml-2 text-[#4073FA]">修改</span> -->
                                    <ElAvatar
                                        :size="60"
                                        :src="userStore.userInfo.avatar"
                                    ></ElAvatar>
                                </CropperUpload>
                                <div class="text-tx-secondary mt-2 text-sm">
                                    建议尺寸：240*240px
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="用户昵称">
                            <div>
                                <el-input v-model="nickName" class="!w-[300px]"></el-input>
                            </div>
                        </el-form-item>
                        <el-form-item label="用户ID">
                            <div class="flex items-center">
                                <span class="mr-4">{{ userStore.userInfo.sn }}</span>
                                <el-button
                                    type="primary"
                                    :link="true"
                                    @click="copy(userStore.userInfo.sn)"
                                >
                                    复制
                                </el-button>
                            </div>
                        </el-form-item>
                        <el-form-item label="上级邀请人">
                            <div>
                                {{ userStore.userInfo.leader_nickname || '系统' }}
                            </div>
                        </el-form-item>
                        <el-form-item label="注册时间">
                            <div>
                                {{ userStore.userInfo.create_time }}
                            </div>
                        </el-form-item>
                        <el-form-item label="邮箱号码" v-if="userStore.userInfo.email">
                            <div class="flex items-center">
                                <span>{{ userStore.userInfo.email }}</span>
                                <span class="ml-2 text-tx-placeholder">
                                    （如需修改，请前往移动端修改）
                                </span>
                            </div>
                        </el-form-item>
                        <el-form-item label="手机号码">
                            <div class="flex items-center">
                                <span>{{ userStore.userInfo.mobile }}</span>
                                <el-button
                                    class="ml-4"
                                    type="primary"
                                    link
                                    @click="openBindPop"
                                >
                                    {{ userStore.userInfo.mobile ? '点击更改' : '立即绑定' }}
                                </el-button>
                            </div>
                        </el-form-item>
                        <el-form-item label="登录密码">
                            <div class="flex items-center">
                                <el-button type="primary" link @click="openPwdPop"
                                >点击设置</el-button>
                            </div>
                        </el-form-item>
                        <el-form-item label="绑定微信">
                            <div class="flex items-center">
                                <span v-if="userStore.userInfo.is_auth">已绑定</span>
                                <el-button
                                    v-else
                                    type="primary"
                                    :link="true"
                                    @click="changeWechat"
                                >
                                    点击绑定
                                </el-button>
                            </div>
                        </el-form-item>

                        <el-form-item label-width="0">
                            <el-button type="primary" class="save-btn" @click="submit">
                                保存
                            </el-button>
                        </el-form-item>

                        <div class="line mb-[30px]"></div>
                        <el-form-item
                            label="注销账号"
                            v-if="appStore.getAccountCancelledStatus"
                        >
                            <div>
                                <div class="text-primary cursor-pointer" @click="toCancelled">
                                    立即注销 >
                                </div>
                                <div class="form-tips">
                                    注销你的账户后，你将无法使用我们的任何服务，并且与你的账户相关的所有数据都将永久丢失。此操作不可逆！
                                </div>
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </ElScrollbar>

        <bindmobile-pop
            v-if="popBinShow"
            ref="bindPopRef"
            @close="
            () => {
              popBinShow = false
              userStore.getUser()
            }
      "
        ></bindmobile-pop>

        <change-pwd-pop
            v-if="popPwdShow"
            ref="pwdPopRef"
            :mobile="userStore.userInfo.mobile"
            :email="userStore.userInfo.email"
            @close="
            () => {
              popPwdShow = false
            }
          "
        ></change-pwd-pop>
    </div>
</template>

<script setup lang="ts">
import {useUserStore} from '@/stores/user'
import BindmobilePop from './_components/bindmobilePop.vue'
import {editUserInfo, cancelled} from '@/api/account'
import {userEdit} from '~/api/user'
import {useAppStore} from '~/stores/app'
import ChangePwdPop from '~/pages/user/index/_components/changePwdPop.vue'
import {LoginPopupTypeEnum} from "~/enums/appEnums";

const router = useRouter()

const popBinShow = ref(false)
const bindPopRef = shallowRef()
const popPwdShow = ref(false)
const pwdPopRef = shallowRef()

const appStore = useAppStore()
const userStore = useUserStore()

const nickName = ref(unref(userStore.userInfo.nickname))

const openBindPop = async () => {
    popBinShow.value = true
    await nextTick()
    bindPopRef.value.open()
}

const openPwdPop = async () => {
    popPwdShow.value = true
    await nextTick()
    pwdPopRef.value.open()
}

const setUserInfo = async (value: any, field: any) => {
    try {
        await userEdit({value, field})
        userStore.getUser()
    } catch (error) {
        feedback.msgError(error)
    }
}

// 绑定微信
const changeWechat = () => {
    userStore.setLoginPopupType(LoginPopupTypeEnum.BIND_WEIXIN)
    userStore.toggleShowLogin(true)
}

const oaBindWx = async (options: any = { getUrl: true }) => {
    let data = null
    const { code, key = '', getUrl } = options

    if (code) {
        data = await oaAuthBind({
            code,
            key
        })
        if (data) {
            refresh()
        }
    }
    if (getUrl) {
        await wechatOa.getUrl()
    }
}

//保存
const submit = async () => {
    await editUserInfo({field: 'nickname', value: nickName.value})
    userStore.getUser()
}

//注销账号
const toCancelled = async () => {
    await feedback.confirm('确认注销账号吗？注销后将无法登录！')
    await cancelled()
    router.push('/')
    userStore.logout()
}
</script>

<style scoped lang="scss">
.save-btn {
    margin-top: 24px;
    margin-bottom: 20px;
    padding: 11px 30px;
    border-radius: 6px;
    border: none;
}

.line {
    width: 100%;
    height: 1px;
    @apply bg-br-light;
}
</style>
