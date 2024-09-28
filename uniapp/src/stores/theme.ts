import { getDecorate } from '@/api/shop'
import { generateVars } from '@/utils/theme'
import { defineStore } from 'pinia'

interface ThemeStore {
    primaryColor: string
    minorColor: string
    btnColor: string
    navColor: string
    navBgColor: string
    vars: string
}
export const useThemeStore = defineStore({
    id: 'themeStore',
    state: (): ThemeStore => ({
        primaryColor: '#3C5EFD',
        minorColor: '#54C6EE',
        btnColor: 'white',
        navColor: '#000000',
        navBgColor: '#ffffff',
        vars: ''
    }),
    actions: {
        async getTheme() {
            // const data = await getDecorate({
            //     id: 4
            // })
            // const {
            //     themeColor1,
            //     themeColor2,
            //     buttonColor,
            //     navigationBarColor,
            //     topTextColor
            // } = JSON.parse(data.data)
            // this.primaryColor = themeColor1
            // this.minorColor = themeColor2
            // this.btnColor = buttonColor
            // this.navColor = topTextColor === 'white' ? '#ffffff' : '#000000'
            // this.navBgColor = navigationBarColor || themeColor1
            this.vars = generateVars(
                {
                    primary: this.primaryColor
                },
                {
                    '--color-minor': this.minorColor
                }
            )
        },
        setTheme(color: string) {
            this.primaryColor = color
        }
    }
})
