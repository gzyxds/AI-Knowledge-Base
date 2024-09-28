const config = () => ({
    title: '引导设置',
    name: 'guide',
    isShow: true,
    prop: {
        bgImage: '',
        content: '',
        logoImage: '',
        isShowLeft: 1,

        column1: '',
        columnMenu1: [],
        column2: '',
        columnMenu2: [],

        rightQrcode1: '',
        rightQrcodeTitle1: '',
        rightQrcodeShow1: 1,
        rightQrcode2: '',
        rightQrcodeTitle2: '',
        rightQrcodeShow2: 1,
    }
})

export type Prop = ReturnType<typeof config>['prop']
export default config
