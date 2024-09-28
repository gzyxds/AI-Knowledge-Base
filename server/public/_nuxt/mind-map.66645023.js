import{_ as S}from"./index.vue.7f39d825.js";import{a5 as I,ah as M,d as B}from"./entry.dfbfd4fd.js";import E from"./collapse.2ae4ae12.js";import{T as R,e as D}from"./index.15cf1258.js";import{k as T,s as h,a as N,b as v,E as V,n as y,H as p,U as k,V as a,X as o,J as l,T as q,u as d,I as L,ag as U,ah as W}from"./swiper-vue.2eb6bd20.js";import{_ as Z}from"./_plugin-vue_export-helper.c27b6911.js";import"./el-collapse.1527f898.js";import"./castArray.c741e965.js";import"./index.93ec1a3c.js";import"./index.edaf9b5e.js";const A=m=>(U("data-v-6ed9d3f4"),m=m(),W(),m),F=A(()=>l("span",{class:"text-2xl ml-1"}," 脑图 ",-1)),H={class:"h-[600px] rounded-[12px] border border-solid border-br-light relative"},J={class:"toolbar"},O={class:"toolbar-item"},Q={class:"toolbar-item"},X={class:"toolbar-item"},j={class:"toolbar-item"},G={class:"toolbar-item"},K=T({__name:"mind-map",props:{content:{default:""},quote:{default:()=>[]}},setup(m){const u=m,$=new R,x=h(),w=h(),z=I(),f=N(!1);let t=null;const C=n=>{const e=/(`{3}[\s\S]*?`{3}(?:(?!.)))|(`{3}[\s\S]*)|(`[\s\S]*?`{1}?)|(`[\s\S]*)|(?:\[(?:(?:number )|\^)?([\d]{1,2})\])/g;return n.replaceAll(e,function(i,r,c,s,P,g){const _=u.quote[Number(g)-1];return _?`<a href="${_.seeMoreUrl}" 
title="${_.title}"
target="_blank"
style="display: inline-block;
width: 15px;
height: 15px;
border-radius: 50%;
font-size: 12px;
text-align: center;
background-color: var(--el-fill-color-lighter);
text-align: center;
font-size: 9px;
color:var(--el-text-color-secondary);
text-decoration: none !important;
vertical-align: middle;
margin: 0 2px 3px;
cursor: pointer;
line-height: 16px;">${g}</a>`:""})},b=n=>{n=C(n);const{root:e}=$.transform(n);t==null||t.setData(e),t==null||t.fit()};return v(z,n=>{n?document.documentElement.classList.add("markmap-dark"):document.documentElement.classList.remove("markmap-dark")},{immediate:!0}),v(()=>u.content,b),V(async()=>{await y(),t=D.create(x.value),b(u.content)}),v(f,async()=>{await y(),t==null||t.fit()}),(n,e)=>{const i=S,r=B;return p(),k(E,null,{title:a(()=>[o(i,{name:"local-icon-mind_map",size:16}),F]),default:a(()=>[l("div",H,[l("div",{ref_key:"svgWrapRef",ref:w,class:q(["w-full h-full",{"!fixed top-0 left-0 w-screen h-screen z-[9999] bg-body":d(f)}])},[l("div",J,[l("div",O,[d(f)?(p(),k(r,{key:0,link:"",onClick:e[0]||(e[0]=c=>f.value=!1)},{icon:a(()=>[o(i,{name:"local-icon-fullscreen-exit",size:18})]),_:1})):(p(),k(r,{key:1,link:"",onClick:e[1]||(e[1]=c=>f.value=!0)},{icon:a(()=>[o(i,{name:"local-icon-fullscreen",size:18})]),_:1}))]),l("div",Q,[o(r,{link:"",onClick:e[2]||(e[2]=c=>{var s;return(s=d(t))==null?void 0:s.fit()})},{icon:a(()=>[o(i,{name:"el-icon-Refresh",size:20})]),_:1})]),l("div",X,[o(r,{link:"",onClick:e[3]||(e[3]=c=>{var s;return(s=d(t))==null?void 0:s.rescale(1.25)})},{icon:a(()=>[o(i,{name:"el-icon-ZoomIn",size:20})]),_:1})]),l("div",j,[o(r,{link:"",onClick:e[4]||(e[4]=c=>{var s;return(s=d(t))==null?void 0:s.rescale(.8)})},{icon:a(()=>[o(i,{name:"el-icon-ZoomOut",size:20})]),_:1})]),l("div",G,[o(r,{link:"",onClick:e[5]||(e[5]=c=>d(M)(n.content))},{icon:a(()=>[o(i,{name:"el-icon-CopyDocument",size:20})]),_:1})])]),(p(),L("svg",{ref_key:"svgRef",ref:x,class:"w-full h-full"},null,512))],2)])]),_:1})}}});const ce=Z(K,[["__scopeId","data-v-6ed9d3f4"]]);export{ce as default};
