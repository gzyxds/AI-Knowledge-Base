export interface FunctionPointItem {
    text: string
}

export interface DataItem {
    image: string
    title: string
    subtitle: string
    isShow: boolean
    functionPoint: FunctionPointItem[]
}

const config = () => ({
    title: '功能介绍',
    name: 'intro',
    isShow: true,
    prop: {
        data: [] as DataItem[]
    }
})

export type Prop = ReturnType<typeof config>['prop']
export default config
