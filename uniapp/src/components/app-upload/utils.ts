/**
 * @description 获取文件扩展名和文件名
 * @param name
 * @returns
 */
export const getFileExt = (name: string) => {
    const lastLen = name.lastIndexOf('.')
    const len = name.length
    const fileName = name.substring(0, lastLen)
    const ext = name.substring(lastLen + 1, len)
    return {
        name: fileName,
        ext
    }
}

/**
 * @description 过滤可以上传的文件
 * @returns
 */
export const filterFile = (files: any[], extname: string[] = []) => {
    const newFiles: any[] = []
    if (!extname.length) {
        return files
    }

    files.forEach((v) => {
        const fileFullName = getFileExt(v.name)
        const ext = fileFullName.ext.toLowerCase()
        if (extname.indexOf(ext) !== -1) {
            newFiles.push(v)
        }
    })
    if (newFiles.length !== files.length) {
        uni.showToast({
            title: `当前选择了${files.length}个文件 ，${
                files.length - newFiles.length
            } 个文件格式不正确，已自动剔除`,
            icon: 'none',
            duration: 5000
        })
    }
    return newFiles
}
