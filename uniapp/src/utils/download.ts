/**
 * @description 下载文件
 * @param {String} url  文件url
 * @param {String} name 下载的文件名称
 */
export const download = (url: string, name: string) => {
    const aTag = document.createElement('a')
    document.body.appendChild(aTag)
    aTag.href = url
    aTag.download = name
    aTag.click()
    aTag.remove()
}

export const downloadFile = async (
    url: string,
    name: string,
    type = 'image'
) => {
    if (!url) return uni.$u.toast('文件地址错误')
    uni.showLoading({ title: '下载中' })
    try {
        const res: any = await uni.downloadFile({ url, timeout: 10000 })
        url = res.tempFilePath
        //#ifdef H5
        download(url, name)
        //#endif
        //#ifndef H5
        const saveApi =
            type === 'image'
                ? uni.saveImageToPhotosAlbum
                : uni.saveVideoToPhotosAlbum
        await saveApi({
            filePath: url
        })
        uni.showToast({
            title: '文件保存成功',
            icon: 'success'
        })
        //#endif
        uni.hideLoading()
    } catch (error: any) {
        uni.hideLoading()
        if (error.errMsg == 'downloadFile:fail fail:timeout') {
            uni.$u.toast('文件下载超时，请重新下载')
            return
        }
        if (error.errMsg.indexof('fail auth deny')) {
            const res: UniApp.ShowModalRes = await uni.showModal({
                title: '提示',
                content: '您关闭了权限，请前往设置打开权限'
            })
            if (res.confirm) {
                const setting = await uni.openSetting()
                if (setting.authSetting['scope.writePhotosAlbum']) {
                    uni.showModal({
                        title: '提示',
                        content: '获取权限成功,再次保存文件即可成功',
                        showCancel: false
                    })
                } else {
                    uni.showModal({
                        title: '提示',
                        content: '获取权限失败，无法保存到相册',
                        showCancel: false
                    })
                }
            }
            return
        }
        uni.$u.toast('文件下载失败，请重试')
    }
}
