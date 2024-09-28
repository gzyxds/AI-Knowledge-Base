window.addEventListener('load', function () {
  const chatContainer = document.createElement('div')
  const iframeContainer = document.createElement('div')
  const iconContainer = document.createElement('div')
  const closeContainer = document.createElement('div')
  const linkPreload = document.createElement('link')

  const chatIcon = `<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30">
  <g data-name="评论、消息-06" transform="translate(-41.933 -120)">
    <rect data-name="矩形 8968"  transform="translate(41.933 120)" fill="currentColor" opacity="0"/>
    <g data-name="组 19263" transform="translate(-5 -5.386)">
      <path data-name="路径 24891" d="M55.053,152.771a1.7,1.7,0,0,1-1.72-1.72v-1.032a1.923,1.923,0,0,0-1.927-1.927,4.464,4.464,0,0,1-4.472-4.472V132.472a4.541,4.541,0,0,1,4.4-4.472H72.461a4.464,4.464,0,0,1,4.472,4.472V143.55a4.464,4.464,0,0,1-4.472,4.472h-10.6a2.18,2.18,0,0,0-1.583.619l-4.06,3.647a1.952,1.952,0,0,1-1.17.482Zm-3.716-22.706a2.438,2.438,0,0,0-2.408,2.408V143.55a2.438,2.438,0,0,0,2.408,2.408,4.053,4.053,0,0,1,4.06,3.991v.206l3.509-3.1a4.276,4.276,0,0,1,2.959-1.1h10.6a2.438,2.438,0,0,0,2.408-2.408V132.472a2.438,2.438,0,0,0-2.408-2.408Z" fill="#fff"/>
      <path data-name="路径 24892" d="M257.376,398.176m-1.376,0a1.376,1.376,0,1,0,1.376-1.376A1.376,1.376,0,0,0,256,398.176Z" transform="translate(-202.324 -260.131)" fill="#fff"/>
      <path data-name="路径 24893" d="M470.71,398.176m-1.376,0a1.376,1.376,0,1,0,1.376-1.376A1.376,1.376,0,0,0,469.333,398.176Z" transform="translate(-408.776 -260.131)" fill="#fff"/>
      <path data-name="路径 24894" d="M684.043,398.176m-1.376,0a1.376,1.376,0,1,0,1.376-1.376A1.376,1.376,0,0,0,682.667,398.176Z" transform="translate(-615.229 -260.131)" fill="#fff"/>
    </g>
  </g>
</svg>`
  const closeIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M764.288 214.592 512 466.88 259.712 214.592a31.936 31.936 0 0 0-45.12 45.12L466.752 512 214.528 764.224a31.936 31.936 0 1 0 45.12 45.184L512 557.184l252.288 252.288a31.936 31.936 0 0 0 45.12-45.12L557.12 512.064l252.288-252.352a31.936 31.936 0 1 0-45.12-45.184z"></path></svg>`
  chatContainer.setAttribute('class', 'chat-container')
  iframeContainer.setAttribute('class', 'iframe-container')
  closeContainer.setAttribute('class', 'close-container')
  closeContainer.setAttribute(
    'style',
    `
  position: absolute;
  right: 10px;
  top: 16px;
  height: 25px;
  width: 25px;
  color:#999;
  cursor:pointer;
  `
  )
  iframeContainer.setAttribute(
    'style',
    `
  display: none;
  position: fixed;
  right: 44px;
  bottom: 44px;
  z-index: 999999;
  width: ${window.chat_iframe_width};
  height: ${window.chat_iframe_height};
  border: 1px solid #DCDFE6;
`
  )
  iconContainer.setAttribute('class', 'icon-container')

  iconContainer.setAttribute(
    'style',
    `
  box-sizing: border-box;
  position: fixed;
  right: 44px;
  bottom: 44px;
  z-index: 99999;
  width: 44px;
  height: 44px;
  padding: 12px;
  border-radius: 100%;
  cursor: pointer;
  background-color: ${window.chat_icon_bg || '#3C5EFD'};
  color: ${window.chat_icon_color || '#fff'};
`
  )
  iconContainer.addEventListener('click', function () {
    iframeContainer.style.display = 'block'
  })
  closeContainer.addEventListener('click', function () {
    iframeContainer.style.display = 'none'
  })
  iconContainer.innerHTML = chatIcon
  iframeContainer.innerHTML = `<iframe 
    src="${window.chat_iframe_src}" 
    width="100%" 
    height="100%" 
    frameborder="0">
    </iframe>`
  linkPreload.setAttribute('rel', 'preload')
  linkPreload.setAttribute('href', window.chat_iframe_src)
  linkPreload.setAttribute('as', 'document')

  closeContainer.innerHTML = closeIcon
  iframeContainer.appendChild(closeContainer)
  chatContainer.appendChild(iframeContainer)
  chatContainer.appendChild(iconContainer)
  document.head.appendChild(linkPreload)
  document.body.appendChild(chatContainer)
})
