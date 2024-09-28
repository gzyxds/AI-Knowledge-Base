import { getWxCodeUrl, wxJsConfig } from '@/api/account'
import wx from 'weixin-js-sdk'

const wechatoa = {
    _authData: {
        code: '',
        scene: ''
    },
    setAuthData(data: any = {}) {
        this._authData = data
    },
    getAuthData() {
        return this._authData
    },
    getSignLink() {
        // if (typeof window.signLink === 'undefined' || window.signLink === '') {
        //     window.signLink = location.href.split('#')[0]
        // }
        return location.href.split('#')[0]
    },
    getUrl(): Promise<void> {
        // const currentUrl = `${location.href}${
        //     location.search ? '&' : '?'
        // }scene=${scene || ''}&${objectToQuery(extra)}`
        return new Promise((resolve, reject) => {
            getWxCodeUrl().then((res) => {
                console.log(res.url)
                location.href = res.url
                resolve()
            }, reject)
        })
    },
    config() {
        return new Promise((resolve, reject) => {
            wxJsConfig({
                url: this.getSignLink()
            }).then((res) => {
                wx.config({
                    ...res,
                    success: () => {
                        resolve('success')
                    },
                    fail: (res: any) => {
                        reject('wx config is fail')
                    }
                })
            })
        })
    },
    miniProgram: wx?.miniProgram,
    ready(): Promise<void> {
        return new Promise((resolve, reject) => {
            wx.ready(() => {
                resolve()
            })
            wx.error(() => {
                reject()
            })
        })
    },
    pay(options: Record<any, any>, resolve, reject) {
        // return new Promise((resolve, reject) => {
        this.ready()
            .then(() => {
                wx.chooseWXPay({
                    timestamp: options.timeStamp,
                    nonceStr: options.nonceStr,
                    package: options.package,
                    signType: options.signType,
                    paySign: options.paySign,
                    success: (res: any) => {
                        if (res.errMsg === 'chooseWXPay:ok') {
                            resolve(res)
                        } else {
                            reject(res.errMsg)
                        }
                    },
                    cancel: (res: any) => {
                        reject(res)
                        this.getUrl()
                    },
                    fail: (res: any) => {
                        reject(res)
                    }
                })
                this.setAuthData({ code: '' })
            })
            .catch((err) => {
                reject(err)
            })
        // })
    }
}

export default wechatoa
