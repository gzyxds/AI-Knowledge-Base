<template>
    <el-collapse model-value="name">
        <el-collapse-item name="name">
            <template #title>
                <slot name="title" />
            </template>
            <slot />
        </el-collapse-item>
    </el-collapse>
</template>

<style lang="scss" scoped>
.el-collapse {
    border: none;
}
</style>
