const config = () => ({
    title: '导航设置',
    name: 'header',
    isShow: true,
    prop: {
        isShowIcon: true,
        nav: [{}, {}, {}, {}, {}] as any[],
        isShowAccount: true
    }
})

export type Prop = ReturnType<typeof config>['prop']
export default config
