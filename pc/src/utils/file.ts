export function dataURLtoFile(dataUrl: string, filename: string) {
  const arr = dataUrl.split(',')
  const mime = arr[0]?.match(/:(.*?);/u)?.[1]
  if (!mime) {
    throw 'dataUrl 解析错误'
  }
  const bstr = atob(arr[1])
  let n = bstr.length
  const u8arr = new Uint8Array(n)
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n)
  }
  const blob = new Blob([u8arr], { type: mime })
  return new File([blob], filename, { type: mime, lastModified: Date.now() })
}

export function downloadFile(url: string, name: string) {
  const aTag = document.createElement('a')
  document.body.appendChild(aTag)
  aTag.href = url
  aTag.download = name
  aTag.click()
  aTag.remove()
}
