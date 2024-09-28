export default () => ({
    title: '热门创作',
    name: 'index-hot',
    content: {
        title: '热门创作',
        showType: 1,
        dataType: 1, //1系统推荐2手动选择
        dataNum: 10,
        data: [] as any[]
    },
    styles: {},
    isHidden: false
})
