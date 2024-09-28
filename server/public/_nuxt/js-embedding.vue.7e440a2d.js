import{_ as b}from"./index.vue.74e23ebc.js";import{E as g,a as j}from"./index.883ed08b.js";import"./entry.dfbfd4fd.js";import"./el-form-item.4ed993c7.js";/* empty css                */import{P as y}from"./index.e94bc8a7.js";import{k,s as l,l as s,H as E,I as C,X as t,V as o,J as n,u as m}from"./swiper-vue.2eb6bd20.js";const $=n("div",{class:"flex items-start"},[n("div",{class:"mr-auto"}," 要在您网站的任何位置添加聊天智能体，请将此 iframe 添加到您的 html 代码中 ")],-1),R={class:"flex-1 min-w-0 rounded-md overflow-hidden"},B=n("div",{class:"flex items-start"},[n("div",{class:"mr-auto"}," 要在您网站的右下角添加聊天气泡，请复制添加到您的 html中 ")],-1),I={class:"flex-1 min-w-0 rounded-md overflow-hidden"},S=k({__name:"js-embedding",props:{channelId:{}},emits:["confirm"],setup(d,{expose:f,emit:F}){const p=d,_=l(),a=l(),h=()=>{var e;(e=a.value)==null||e.open()},u=()=>{var e;(e=a.value)==null||e.close()},r=s(()=>`${location.origin}/chat/${p.channelId}`),w=s(()=>`\`\`\`html
<iframe 
    src="${r.value}" 
    class="chat-iframe"
    frameborder="0"
>
</iframe>
<style>
    /* iframe框默认占满全屏，可根据需求自行调整样式  */
    .chat-iframe {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        z-index: 9999;
    }
</style>
\`\`\``),v=s(()=>`\`\`\`html
<script>
    window.chat_iframe_src = '${r.value}'
    window.chat_iframe_width = '375px' //聊天窗口宽
    window.chat_iframe_height = '667px'  //聊天窗口高
    window.chat_icon_bg = '#3C5EFD' //聊天悬浮按钮背景
    window.chat_icon_color = '#fff' //聊天悬浮按钮颜色
    var js = document.createElement('script')
    js.type = 'text/javascript'
    js.async = true
    js.src = '${location.origin}/js-iframe.js'
    var header = document.getElementsByTagName('head')[0]
    header.appendChild(js)
<\/script>
\`\`\`
`);return f({open:h,close:u}),(e,N)=>{const i=b,c=g,x=j;return E(),C("div",null,[t(y,{ref_key:"popupRef",ref:a,title:"JS嵌入",async:!0,width:"900px","confirm-button-text":"","cancel-button-text":""},{default:o(()=>[t(x,{ref_key:"formRef",ref:_,"label-position":"top","label-width":"84px"},{default:o(()=>[t(c,null,{label:o(()=>[$]),default:o(()=>[n("div",R,[t(i,{content:m(w)},null,8,["content"])])]),_:1}),t(c,null,{label:o(()=>[B]),default:o(()=>[n("div",I,[t(i,{content:m(v)},null,8,["content"])])]),_:1})]),_:1},512)]),_:1},512)])}}});export{S as _};
