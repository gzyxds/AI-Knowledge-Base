<template>
    <div class="user-info flex items-center px-[25px] pt-[20px] pb-[40px]">
        <img src="./images/default_avatar.png" class="w-[60px] h-[60px]" alt="" />
        <div class="ml-[10px]">
            <!-- <div class="text-[18px]">小贝</div>
            <div class="form-tips">学号：123456 复制</div> -->
            <div class="text-2xl text-[#333]">未登录</div>
        </div>
    </div>
</template>
<script lang="ts" setup></script>

<style lang="scss" scoped>
.user-info {
    // background: url(./images/my_topbg.png);
    // background-color:
    // @extend .bg-primary;
    height: 115px;
    background: linear-gradient(
            44.7deg,
            #eaffff 0%,
            #faf6ff 50%,
            #f2f3ff 63%,
            #eaffff 100%
    );
}
</style>
