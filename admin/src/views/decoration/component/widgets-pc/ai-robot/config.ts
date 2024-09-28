const config = () => ({
    title: '背景设置',
    name: 'ai-robot',
    isShow: true,
    prop: {
        banner_bg: '',
        title: '',
        title_color: 1
    }
})

export type Prop = ReturnType<typeof config>['prop']
export default config
