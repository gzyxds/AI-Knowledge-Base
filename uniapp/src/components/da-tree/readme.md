# da-tree

一个基于 Vue3 的 tree(树)组件，同时支持主题换色，可能是最适合你的 tree(树)组件

组件一直在更新，遇到问题可在下方讨论。

`同时更新 Vue2 版本，在此查看 ===>` **[Vue2 版](https://ext.dcloud.net.cn/plugin?id=12692)**

### 关于使用

可在右侧的`使用 HBuilderX 导入插件`或`下载示例项目ZIP`，方便快速上手。

可通过下方的示例及文档说明，进一步了解使用组件相关细节参数。

插件地址：https://ext.dcloud.net.cn/plugin?id=12384

### 组件示例

```jsx
<template>
  <view>多选</view>
  <view><button @click="doCheckedTree(['2'],true)">全选</button></view>
  <view><button @click="doCheckedTree(['2'],false)">取消全选</button></view>
  <view><button @click="doCheckedTree(['211','222'],true)">选中指定节点</button></view>
  <view><button @click="doCheckedTree(['211','222'],false)">取消选中指定节点</button></view>
  <view><button @click="doExpandTree('all',true)">展开全部节点</button></view>
  <view><button @click="doExpandTree('all',false)">收起全部节点</button></view>
  <view><button @click="doExpandTree(['22','23'],true)">展开节点</button></view>
  <view><button @click="doExpandTree(['22','23'],false)">收起节点</button></view>
  <DaTree
    ref="DaTreeRef"
    :data="roomTreeData"
    labelField="name"
    valueField="id"
    defaultExpandAll
    showCheckbox
    :defaultCheckedKeys="defaultCheckedKeysValue"
    @change="handleTreeChange"
    @expand="handleExpandChange"></DaTree>
  <view>单选</view>
  <DaTree
    :data="roomTreeData"
    labelField="name"
    valueField="id"
    defaultExpandAll
    :defaultCheckedKeys="defaultCheckedKeysValue2"
    @change="handleTreeChange"
    @expand="handleExpandChange"></DaTree>
  <view>默认展开指定节点</view>
  <DaTree
    :data="roomTreeData"
    labelField="name"
    valueField="id"
    showCheckbox
    :defaultExpandedKeys="defaultExpandKeysValue3"
    @change="handleTreeChange"
    @expand="handleExpandChange"></DaTree>
  <view>异步加载数据</view>
  <DaTree
    :data="roomTreeData"
    labelField="name"
    valueField="id"
    showCheckbox
    loadMode
    :loadApi="GetApiData"
    defaultExpandAll
    @change="handleTreeChange"
    @expand="handleExpandChange"></DaTree>
</template>
```

```js
import { defineComponent, ref } from 'vue'

/**
 * 模拟创建一个接口数据
 */
function GetApiData(currentNode) {
  const { key } = currentNode

  return new Promise((resolve) => {
    setTimeout(() => {
      // 模拟返回空数据
      if (key.indexOf('-') > -1) {
        return resolve(null)
        // return resolve([])
      }

      return resolve([
        {
          id: `${key}-1`,
          name: `行政部X${key}-1`,
        },
        {
          id: `${key}-2`,
          name: `财务部X${key}-2`,
          append: '定义了末项数据',
          leaf: true,
        },
        {
          id: `${key}-3`,
          name: `资源部X${key}-3`,
        },
        {
          id: `${key}-4`,
          name: `资源部X${key}-3`,
          append: '被禁用，无展开图标',
          disabled: true,
        },
      ])
    }, 2000)
  })
}

import DaTree from '@/components/da-tree/index.vue'
export default defineComponent({
  components: { DaTree },
  setup() {
    const DaTreeRef = ref()
    // key的类型必须对应树数据key的类型
    const defaultCheckedKeysValue = ref(['211', '222'])
    const defaultCheckedKeysValue2 = ref('222')
    const defaultExpandKeysValue3 = ref(['212', '231'])
    const roomTreeData = ref([
      {
        id: '2',
        name: '行政中心',
        children: [
          {
            id: '21',
            name: '行政部',
            children: [
              {
                id: '211',
                name: '行政一部',
                children: null,
              },
              {
                id: '212',
                name: '行政二部',
                children: [],
                disabled: true,
              },
            ],
          },
          {
            id: '22',
            name: '财务部',
            children: [
              {
                id: '221',
                name: '财务一部',
                children: [],
                disabled: true,
              },
              {
                id: '222',
                name: '财务二部',
                children: [],
              },
            ],
          },
          {
            id: '23',
            name: '人力资源部',
            children: [
              {
                id: '231',
                name: '人力一部',
                children: [],
              },
              {
                id: '232',
                name: '人力二部',
                append: '更多示例，请下载示例项目查看',
              },
            ],
          },
        ],
      },
    ])
    function doExpandTree(keys, expand) {
      DaTreeRef.value?.setExpandedKeys(keys, expand)

      const gek = DaTreeRef.value?.getExpandedKeys()
      console.log('当前已展开的KEY ==>', gek)
    }
    function doCheckedTree(keys, checked) {
      DaTreeRef.value?.setCheckedKeys(keys, checked)

      const gek = DaTreeRef.value?.getCheckedKeys()
      console.log('当前已选中的KEY ==>', gek)
    }
    function handleTreeChange(allSelectedKeys, currentItem) {
      console.log('handleTreeChange ==>', allSelectedKeys, currentItem)
    }
    function handleExpandChange(expand, currentItem) {
      console.log('handleExpandChange ==>', expand, currentItem)
    }
    return {
      DaTreeRef,
      roomTreeData,
      defaultCheckedKeysValue,
      defaultCheckedKeysValue2,
      defaultExpandKeysValue3,
      handleTreeChange,
      handleExpandChange,
      GetApiData,
      doExpandTree,
      doCheckedTree,
    }
  },
})
```

** 更多示例请下载/导入示例项目 ZIP 查看 **

### 组件参数

| 属性                | 类型                            | 默认值     | 必填 | 说明                                                                         |
| :------------------ | :------------------------------ | :--------- | :--- | :--------------------------------------------------------------------------- |
| data                | `Array`                         | -          | 是   | 树的数据                                                                     |
| themeColor          | `String`                        | `#007aff`  | 否   | 主题色，十六进制                                                             |
| defaultCheckedKeys  | `Array` \| `Number` \| `String` | -          | 否   | 默认选中的节点，单选为单个 key，多选为 key 的数组                            |
| showCheckbox        | `Boolean`                       | `false`    | 否   | 是否开启多选，默认单选                                                       |
| checkStrictly       | `Boolean`                       | `false`    | 否   | 多选时，是否执行父子不关联的任意勾选，默认父子关联                           |
| showRadioIcon       | `Boolean`                       | `true`     | 否   | 是否显示单选图标，默认显示                                                   |
| onlyRadioLeaf       | `Boolean`                       | `true`     | 否   | 单选时只允许选中末级，默认可随意选中                                         |
| defaultExpandAll    | `Boolean`                       | `false`    | 否   | 是否默认展开全部                                                             |
| defaultExpandedKeys | `Array`                         | -          | 否   | 默认展开的节点                                                               |
| indent              | `Number`                        | `40`       | 否   | 子项缩进距离，单位 rpx                                                       |
| checkboxPlacement   | `String`                        | `left`     | 否   | 选择框的位置，可选 left/right                                                |
| loadMode            | `Boolean`                       | `false`    | 否   | 为 true 时，空的 children 数组会显示展开图标                                 |
| loadApi             | `Function`                      | -          | 否   | 选择框的位置，可选 left/right                                                |
| checkedDisabled     | `Boolean`                       | `false`    | 否   | 是否渲染禁用值，默认不渲染                                                   |
| packDisabledkey     | `Boolean`                       | `true`     | 否   | 是否返回已禁用的但已选中的 key，默认返回禁用已选值                           |
| expandChecked       | `Boolean`                       | `false`    | 否   | 是否自动展开到选中的节点，默认不展开                                         |
| alwaysFirstLoad     | `Boolean`                       | `false`    | 否   | 是否总在首次的时候加载一下内容，默认不加载，否则只有展开末级节点才会加载数据 |
| isLeafFn            | `Function`                      | -          | 否   | 自定义函数返回来控制数据项的末项                                             |
| field               | `Object`                        | -          | 否   | 字段对应内容，格式参考下方(1.5.0 后移除，请用单独的字段匹配)                 |
| labelField          | `String`                        | `label`    | 否   | 指定节点对象中某个属性为标签字段，默认`label`                                |
| valueField          | `String`                        | `value`    | 否   | 指定节点对象中某个属性为值字段，默认`value`                                  |
| childrenField       | `String`                        | `children` | 否   | 指定节点对象中某个属性为子树节点字段，默认`children`                         |
| disabledField       | `String`                        | `disabled` | 否   | 指定节点对象中某个属性为禁用字段，默认`disabled`                             |
| appendField         | `String`                        | `append`   | 否   | 指定节点对象中某个属性为副标签字段，默认`append`                             |
| leafField           | `String`                        | `leaf`     | 否   | 指定节点对象中某个属性为末级节点字段，默认`leaf`                             |
| sortField           | `String`                        | `sort`     | 否   | 指定节点对象中某个属性为排序字段，默认`sort`                                 |
| filterValue         | `String`                        | -          | 否   | 搜索筛选的关键词，通过输入关键词筛选内容                                     |

**field 格式(1.5.0 后移除，请用单独的字段匹配)**

```js
{
  label: 'label',
  key: 'key',
  children: 'children',
  disabled: 'disabled',
  append: 'append'
}
```

### 组件事件

| 事件名称 | 回调参数                                | 说明            |
| :------- | :-------------------------------------- | :-------------- |
| change   | `(allCheckedKeys, currentItem) => void` | 选中时回调      |
| expand   | `(expandState, currentItem) => void`    | 展开/收起时回调 |

### 组件方法

| 方法名称            | 参数             | 说明                                                                                              |
| :------------------ | :--------------- | :------------------------------------------------------------------------------------------------ |
| setCheckedKeys      | `(keys,checked)` | 设置指定 key 的节点选中/取消选中的状态。注: keys 单选时为 key，多选时为 key 的数组                |
| setExpandedKeys     | `(keys,expand)`  | 设置指定 key 的节点展开/收起的状态，当 keys 为 all 时即代表展开/收起全部。注：keys 为数组或 `all` |
| getCheckedKeys      | -                | 返回已选的 key                                                                                    |
| getHalfCheckedKeys  | -                | 返回半选的 key                                                                                    |
| getUncheckedKeys    | -                | 返回未选的 key                                                                                    |
| getCheckedNodes     | -                | 返回已选的节点                                                                                    |
| getUncheckedNodes   | -                | 返回未选的节点                                                                                    |
| getHalfCheckedNodes | -                | 返回半选的节点                                                                                    |
| getExpandedKeys     | -                | 返回已展开的 key                                                                                  |
| getUnexpandedKeys   | -                | 返回未展开的 key                                                                                  |
| getExpandedNodes    | -                | 返回已展开的节点                                                                                  |
| getUnexpandedNodes  | -                | 返回未展开的节点                                                                                  |

### 组件版本

v1.4.2

### 差异化

已通过测试

> - H5 页面
> - 微信小程序
> - 支付宝、钉钉小程序
> - 字节跳动、抖音、今日头条小程序
> - 百度小程序
> - 飞书小程序
> - QQ 小程序
> - 京东小程序

未测试

> - 快手小程序由于非企业用户暂无演示
> - 快应用、360 小程序因 Vue3 支持的原因暂无演示

### 开发组

[@CRLANG](https://crlang.com)
