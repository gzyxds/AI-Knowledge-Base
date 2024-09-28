// https://nuxt.com/docs/api/configuration/nuxt-config
import { URL, fileURLToPath } from 'node:url'
import { createSvgIconsPlugin } from 'vite-plugin-svg-icons'
import { loadEnv } from 'vite'
const envData = loadEnv(process.env.NODE_ENV!, './')
// console.log(envData)
export default defineNuxtConfig({
    srcDir: 'src/',
    css: [
        '@/assets/styles/index.scss'
    ],
    plugins: ['~/plugins/detectMobile.client.ts', '~/plugins/tinymce.ts'],
    modules: [
        '@pinia/nuxt',
        '@nuxtjs/tailwindcss',
        '@element-plus/nuxt',
        'nuxt-swiper'
    ],
    spaLoadingTemplate: false,
    app: {
        baseURL: envData.VITE_BASE_URL,
        head: {
            title: '',
            meta: [
                {
                    name: 'viewport',
                    content:
                        'width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=0, viewport-fit=cover'
                }
            ]
        }
    },

    runtimeConfig: {
        public: {
            ...envData
        }
    },
    ssr: !!envData.VITE_SSR,
    vite: {
        plugins: [
            createSvgIconsPlugin({
                iconDirs: [
                    fileURLToPath(
                        new URL('./src/assets/icons', import.meta.url)
                    )
                ],
                symbolId: 'local-icon-[dir]-[name]'
            })
        ]
    }
})
