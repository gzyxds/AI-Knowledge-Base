const config = () => ({
    title: '应用场景',
    name: 'entrance',
    isShow: true,
    prop: {
        showType: 3,
        data: [] as any[]
    }
})

export type Prop = ReturnType<typeof config>['prop']
export default config
