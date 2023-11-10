!function(){"use strict";var e,t={823:function(e,t,r){var n=window.wp.blocks,l=window.wp.element,a=window.wp.i18n,s=window.wp.components,o=window.wp.apiFetch,i=r.n(o),c=window.wp.data,d=window.wp.blockEditor,u=r(196),p=r.n(u),m=r(893);!function(e,{insertAt:t}={}){if(!e||typeof document>"u")return;let r=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css","top"===t&&r.firstChild?r.insertBefore(n,r.firstChild):r.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}(".rmsc{--rmsc-main: #4285f4;--rmsc-hover: #f1f3f5;--rmsc-selected: #e2e6ea;--rmsc-border: #ccc;--rmsc-gray: #aaa;--rmsc-bg: #fff;--rmsc-p: 10px;--rmsc-radius: 4px;--rmsc-h: 38px}.rmsc *{box-sizing:border-box;transition:all .2s ease}.rmsc .gray{color:var(--rmsc-gray)}.rmsc .dropdown-content{position:absolute;z-index:1;top:100%;width:100%;padding-top:8px}.rmsc .dropdown-content .panel-content{overflow:hidden;border-radius:var(--rmsc-radius);background:var(--rmsc-bg);box-shadow:0 0 0 1px #0000001a,0 4px 11px #0000001a}.rmsc .dropdown-container{position:relative;outline:0;background-color:var(--rmsc-bg);border:1px solid var(--rmsc-border);border-radius:var(--rmsc-radius)}.rmsc .dropdown-container[aria-disabled=true]:focus-within{box-shadow:var(--rmsc-gray) 0 0 0 1px;border-color:var(--rmsc-gray)}.rmsc .dropdown-container:focus-within{box-shadow:var(--rmsc-main) 0 0 0 1px;border-color:var(--rmsc-main)}.rmsc .dropdown-heading{position:relative;padding:0 var(--rmsc-p);display:flex;align-items:center;width:100%;height:var(--rmsc-h);cursor:default;outline:0}.rmsc .dropdown-heading .dropdown-heading-value{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1}.rmsc .clear-selected-button{cursor:pointer;background:none;border:0;padding:0;display:flex}.rmsc .options{max-height:260px;overflow-y:auto;margin:0;padding-left:0}.rmsc .options li{list-style:none;margin:0}.rmsc .select-item{box-sizing:border-box;cursor:pointer;display:block;padding:var(--rmsc-p);outline-offset:-1px;outline-color:var(--rmsc-primary)}.rmsc .select-item:hover{background:var(--rmsc-hover)}.rmsc .select-item.selected{background:var(--rmsc-selected)}.rmsc .no-options{padding:var(--rmsc-p);text-align:center;color:var(--rmsc-gray)}.rmsc .search{width:100%;position:relative;border-bottom:1px solid var(--rmsc-border)}.rmsc .search input{background:none;height:var(--rmsc-h);padding:0 var(--rmsc-p);width:100%;outline:0;border:0;font-size:1em}.rmsc .search input:focus{background:var(--rmsc-hover)}.rmsc .search-clear-button{cursor:pointer;position:absolute;top:0;right:0;bottom:0;background:none;border:0;padding:0 calc(var(--rmsc-p) / 2)}.rmsc .search-clear-button [hidden]{display:none}.rmsc .item-renderer{display:flex;align-items:baseline}.rmsc .item-renderer input{margin:0 5px 0 0}.rmsc .item-renderer.disabled{opacity:.5}.rmsc .spinner{animation:rotate 2s linear infinite}.rmsc .spinner .path{stroke:var(--rmsc-border);stroke-width:4px;stroke-linecap:round;animation:dash 1.5s ease-in-out infinite}@keyframes rotate{to{transform:rotate(360deg)}}@keyframes dash{0%{stroke-dasharray:1,150;stroke-dashoffset:0}50%{stroke-dasharray:90,150;stroke-dashoffset:-35}to{stroke-dasharray:90,150;stroke-dashoffset:-124}}\n");var h={allItemsAreSelected:"All items are selected.",clearSearch:"Clear Search",clearSelected:"Clear Selected",noOptions:"No options",search:"Search",selectAll:"Select All",selectAllFiltered:"Select All (Filtered)",selectSomeItems:"Select...",create:"Create"},f={value:[],hasSelectAll:!0,className:"multi-select",debounceDuration:200,options:[]},b=p().createContext({}),v=({props:e,children:t})=>{let[r,n]=(0,u.useState)(e.options);return(0,u.useEffect)((()=>{n(e.options)}),[e.options]),(0,m.jsx)(b.Provider,{value:{t:t=>{var r;return(null==(r=e.overrideStrings)?void 0:r[t])||h[t]},...f,...e,options:r,setOptions:n},children:t})},w=()=>p().useContext(b),g={when:!0,eventTypes:["keydown"]};function x(e,t,r){let n=(0,u.useMemo)((()=>Array.isArray(e)?e:[e]),[e]),l=Object.assign({},g,r),{when:a,eventTypes:s}=l,o=(0,u.useRef)(t),{target:i}=l;(0,u.useEffect)((()=>{o.current=t}));let c=(0,u.useCallback)((e=>{n.some((t=>e.key===t||e.code===t))&&o.current(e)}),[n]);(0,u.useEffect)((()=>{if(a&&typeof window<"u"){let e=i?i.current:window;return s.forEach((t=>{e&&e.addEventListener(t,c)})),()=>{s.forEach((t=>{e&&e.removeEventListener(t,c)}))}}}),[a,s,n,i,t])}var y={ARROW_DOWN:"ArrowDown",ARROW_UP:"ArrowUp",ENTER:"Enter",ESCAPE:"Escape",SPACE:"Space"},_=()=>(0,m.jsxs)("svg",{width:"24",height:"24",fill:"none",stroke:"currentColor",strokeWidth:"2",className:"dropdown-search-clear-icon gray",children:[(0,m.jsx)("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),(0,m.jsx)("line",{x1:"6",y1:"6",x2:"18",y2:"18"})]}),k=({checked:e,option:t,onClick:r,disabled:n})=>(0,m.jsxs)("div",{className:"item-renderer "+(n?"disabled":""),children:[(0,m.jsx)("input",{type:"checkbox",onChange:r,checked:e,tabIndex:-1,disabled:n}),(0,m.jsx)("span",{children:t.label})]}),E=({itemRenderer:e=k,option:t,checked:r,tabIndex:n,disabled:l,onSelectionChanged:a,onClick:s})=>{let o=(0,u.useRef)(),i=()=>{l||a(!r)};return x([y.ENTER,y.SPACE],(e=>{i(),e.preventDefault()}),{target:o}),(0,m.jsx)("label",{className:"select-item "+(r?"selected":""),role:"option","aria-selected":r,tabIndex:n,ref:o,children:(0,m.jsx)(e,{option:t,checked:r,onClick:e=>{i(),s(e)},disabled:l})})},S=({options:e,onClick:t,skipIndex:r})=>{let{disabled:n,value:l,onChange:a,ItemRenderer:s}=w();return(0,m.jsx)(m.Fragment,{children:e.map(((e,o)=>{let i=o+r;return(0,m.jsx)("li",{children:(0,m.jsx)(E,{tabIndex:i,option:e,onSelectionChanged:t=>((e,t)=>{n||a(t?[...l,e]:l.filter((t=>t.value!==e.value)))})(e,t),checked:!!l.find((t=>t.value===e.value)),onClick:e=>t(e,i),itemRenderer:s,disabled:e.disabled||n})},(null==e?void 0:e.key)||o)}))})},C=()=>{let{t:e,onChange:t,options:r,setOptions:n,value:l,filterOptions:a,ItemRenderer:s,disabled:o,disableSearch:i,hasSelectAll:c,ClearIcon:d,debounceDuration:p,isCreatable:h,onCreateOption:f}=w(),b=(0,u.useRef)(),v=(0,u.useRef)(),[g,k]=(0,u.useState)(""),[C,j]=(0,u.useState)(r),[N,O]=(0,u.useState)(""),[R,A]=(0,u.useState)(0),I=(0,u.useCallback)(((e,t)=>{let r;return function(...n){clearTimeout(r),r=setTimeout((()=>{e.apply(null,n)}),t)}})((e=>O(e)),p),[]),T=(0,u.useMemo)((()=>{let e=0;return i||(e+=1),c&&(e+=1),e}),[i,c]),P={label:e(g?"selectAllFiltered":"selectAll"),value:""},D=()=>{var e;O(""),k(""),null==(e=null==v?void 0:v.current)||e.focus()},L=e=>A(e);x([y.ARROW_DOWN,y.ARROW_UP],(e=>{switch(e.code){case y.ARROW_UP:B(-1);break;case y.ARROW_DOWN:B(1);break;default:return}e.stopPropagation(),e.preventDefault()}),{target:b});let W=async()=>{let e={label:g,value:g,__isNew__:!0};f&&(e=await f(g)),n([e,...r]),D(),t([...l,e])},B=e=>{let t=R+e;t=Math.max(0,t),t=Math.min(t,r.length+Math.max(T-1,0)),A(t)};(0,u.useEffect)((()=>{var e,t;null==(t=null==(e=null==b?void 0:b.current)?void 0:e.querySelector(`[tabIndex='${R}']`))||t.focus()}),[R]);let[F,H]=(0,u.useMemo)((()=>{let e=C.filter((e=>!e.disabled));return[e.every((e=>-1!==l.findIndex((t=>t.value===e.value)))),0!==e.length]}),[C,l]);(0,u.useEffect)((()=>{(async()=>a?await a(r,N):function(e,t){return t?e.filter((({label:e,value:r})=>null!=e&&null!=r&&e.toLowerCase().includes(t.toLowerCase()))):e}(r,N))().then(j)}),[N,r]);let M=(0,u.useRef)();x([y.ENTER],W,{target:M});let V=h&&g&&!C.some((e=>(null==e?void 0:e.value)===g));return(0,m.jsxs)("div",{className:"select-panel",role:"listbox",ref:b,children:[!i&&(0,m.jsxs)("div",{className:"search",children:[(0,m.jsx)("input",{placeholder:e("search"),type:"text","aria-describedby":e("search"),onChange:e=>{I(e.target.value),k(e.target.value),A(0)},onFocus:()=>{A(0)},value:g,ref:v,tabIndex:0}),(0,m.jsx)("button",{type:"button",className:"search-clear-button",hidden:!g,onClick:D,"aria-label":e("clearSearch"),children:d||(0,m.jsx)(_,{})})]}),(0,m.jsxs)("ul",{className:"options",children:[c&&H&&(0,m.jsx)(E,{tabIndex:1===T?0:1,checked:F,option:P,onSelectionChanged:e=>{let n=(e=>{let t=C.filter((e=>!e.disabled)).map((e=>e.value));if(e){let e=[...l.map((e=>e.value)),...t];return(a?C:r).filter((t=>e.includes(t.value)))}return l.filter((e=>!t.includes(e.value)))})(e);t(n)},onClick:()=>L(1),itemRenderer:s,disabled:o}),C.length?(0,m.jsx)(S,{skipIndex:T,options:C,onClick:(e,t)=>L(t)}):V?(0,m.jsx)("li",{onClick:W,className:"select-item creatable",tabIndex:1,ref:M,children:`${e("create")} "${g}"`}):(0,m.jsx)("li",{className:"no-options",children:e("noOptions")})]})]})},j=({expanded:e})=>(0,m.jsx)("svg",{width:"24",height:"24",fill:"none",stroke:"currentColor",strokeWidth:"2",className:"dropdown-heading-dropdown-arrow gray",children:(0,m.jsx)("path",{d:e?"M18 15 12 9 6 15":"M6 9L12 15 18 9"})}),N=()=>{let{t:e,value:t,options:r,valueRenderer:n}=w(),l=0===t.length,a=t.length===r.length,s=n&&n(t,r);return l?(0,m.jsx)("span",{className:"gray",children:s||e("selectSomeItems")}):(0,m.jsx)("span",{children:s||(a?e("allItemsAreSelected"):t.map((e=>e.label)).join(", "))})},O=({size:e=24})=>(0,m.jsx)("span",{style:{width:e,marginRight:"0.2rem"},children:(0,m.jsx)("svg",{width:e,height:e,className:"spinner",viewBox:"0 0 50 50",style:{display:"inline",verticalAlign:"middle"},children:(0,m.jsx)("circle",{cx:"25",cy:"25",r:"20",fill:"none",className:"path"})})}),R=()=>{let{t:e,onMenuToggle:t,ArrowRenderer:r,shouldToggleOnHover:n,isLoading:l,disabled:a,onChange:s,labelledBy:o,value:i,isOpen:c,defaultIsOpen:d,ClearSelectedIcon:p,closeOnChangedValue:h}=w();(0,u.useEffect)((()=>{h&&g(!1)}),[i]);let[f,b]=(0,u.useState)(!0),[v,g]=(0,u.useState)(d),[k,E]=(0,u.useState)(!1),S=r||j,R=(0,u.useRef)();(function(e,t){let r=(0,u.useRef)(!1);(0,u.useEffect)((()=>{r.current?e():r.current=!0}),t)})((()=>{t&&t(v)}),[v]),(0,u.useEffect)((()=>{void 0===d&&"boolean"==typeof c&&(b(!1),g(c))}),[c]),x([y.ENTER,y.ARROW_DOWN,y.SPACE,y.ESCAPE],(e=>{var t;["text","button"].includes(e.target.type)&&[y.SPACE,y.ENTER].includes(e.code)||(f&&(e.code===y.ESCAPE?(g(!1),null==(t=null==R?void 0:R.current)||t.focus()):g(!0)),e.preventDefault())}),{target:R});let A=e=>{f&&n&&g(e)};return(0,m.jsxs)("div",{tabIndex:0,className:"dropdown-container","aria-labelledby":o,"aria-expanded":v,"aria-readonly":!0,"aria-disabled":a,ref:R,onFocus:()=>!k&&E(!0),onBlur:e=>{!e.currentTarget.contains(e.relatedTarget)&&f&&(E(!1),g(!1))},onMouseEnter:()=>A(!0),onMouseLeave:()=>A(!1),children:[(0,m.jsxs)("div",{className:"dropdown-heading",onClick:()=>{f&&g(!l&&!a&&!v)},children:[(0,m.jsx)("div",{className:"dropdown-heading-value",children:(0,m.jsx)(N,{})}),l&&(0,m.jsx)(O,{}),i.length>0&&null!==p&&(0,m.jsx)("button",{type:"button",className:"clear-selected-button",onClick:e=>{e.stopPropagation(),s([]),f&&g(!1)},disabled:a,"aria-label":e("clearSelected"),children:p||(0,m.jsx)(_,{})}),(0,m.jsx)(S,{expanded:v})]}),v&&(0,m.jsx)("div",{className:"dropdown-content",children:(0,m.jsx)("div",{className:"panel-content",children:(0,m.jsx)(C,{})})})]})},A=e=>(0,m.jsx)(v,{props:e,children:(0,m.jsx)("div",{className:`rmsc ${e.className||"multi-select"}`,children:(0,m.jsx)(R,{})})});const{newsletterBuilder:{fromNames:I=[],templates:T={}}={}}=window,P=I.map((e=>({value:e,label:e})));var D=function(e){let{contentHandler:t,typeHandler:r,imageHandler:n,templateHandler:o,fromNameHandler:c,typeValue:d,templateValue:u,fromNameValue:p}=e;const[m,h]=(0,l.useState)({});(0,l.useEffect)((()=>{Object.keys(m).length>0||i()({path:"/wp-newsletter-builder/v1/email-types"}).then((e=>{h(e)}))}),[m]),(0,l.useEffect)((()=>{!p&&I.length>0&&c(I[0])}),[c,p]);const f=(e,t)=>e.label<t.label?-1:e.label>t.label?1:0,b=e=>{var t;const r=null!==(t=e[d]?.templates)&&void 0!==t?t:[];if(!r.length)return[];const n=r.map((e=>({value:e,label:T[parseInt(e,10)]})));return n.sort(f),n.unshift({label:(0,a.__)("Select a template","wp-newsletter-builder"),value:""}),n},v=async e=>{if(o(e),!e)return;const r=m[d],{image:l,from_name:a}=r;n(parseInt(l,10)),c(a),i()({path:`/wp/v2/nb_template/${e}?context=edit`}).then((e=>{const{content:r}=e;t(r.raw)}))};return(0,l.useEffect)((()=>{if(!d)return;const e=m[d]?.templates;e&&1===e.length&&v(e[0])}),[d]),(0,l.createElement)(l.Fragment,null,(0,l.createElement)(s.SelectControl,{label:(0,a.__)("Select Header Type","wp-newsletter-builder"),value:d,options:(e=>{const t=Object.keys(e).map((t=>({label:e[t].label,value:t})));return t.sort(f),t.unshift({label:(0,a.__)("Select a type","wp-newsletter-builder"),value:""}),t})(m),onChange:r}),b(m).length?(0,l.createElement)(s.SelectControl,{label:(0,a.__)("Select Template","wp-newsletter-builder"),value:u,options:b(m),onChange:v}):null,(0,l.createElement)(s.SelectControl,{label:(0,a.__)("From Name","wp-newsletter-builder"),value:p||m[d]?.from_name,options:P,onChange:c}))},L=window.wp.coreData,W=window.lodash,B=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"wp-newsletter-builder/email-settings","version":"0.1.0","title":"Email Settings","category":"design","icon":"email","description":"Block to set the email subject, preview text, and list.","textdomain":"email-settings","editorScript":"file:index.ts","editorStyle":"file:index.css","style":["file:style-index.css"],"render":"file:render.php"}');(0,n.registerBlockType)(B,{apiVersion:2,edit:function(){const[e,t]=function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;const r=(0,c.useSelect)((t=>e||t("core/editor").getCurrentPostType()),[]),[n,l]=(0,L.useEntityProp)("postType",r,"meta",t),a="function"==typeof l?l:()=>console.error(`Error attempting to set post meta for post type ${r}. Does it have support for custom-fields?`);return["object"==typeof n?n:{},e=>a((0,W.cloneDeep)(e))]}(),{nb_newsletter_subject:r,nb_newsletter_preview:o,nb_newsletter_list:u,nb_newsletter_email_type:p,nb_newsletter_template:m,nb_newsletter_from_name:h}=e,[f,b]=(0,l.useState)([]),v=Array.isArray(u)?u:[u],w=f.length>0?f.map((e=>({label:e.Name,value:e.ListID}))):[],g=w.filter((e=>v.includes(e.value)));return(0,l.useEffect)((()=>{f.length>0||i()({path:"/wp-newsletter-builder/v1/lists"}).then((e=>{b(e)}))}),[f]),(0,l.createElement)("div",(0,d.useBlockProps)(),(0,l.createElement)(D,{typeValue:p,contentHandler:e=>{(0,c.dispatch)("core/block-editor").resetBlocks((0,n.parse)(e))},typeHandler:e=>{t({nb_newsletter_email_type:e})},imageHandler:e=>{t({nb_newsletter_header_img:e})},templateHandler:e=>{t({nb_newsletter_template:e})},fromNameHandler:e=>{t({nb_newsletter_from_name:e})},templateValue:m,fromNameValue:h}),(0,l.createElement)(s.TextControl,{label:(0,a.__)("Subject","wp-newsletter-builder"),placeholder:(0,a.__)("Enter subject","wp-newsletter-builder"),value:r,onChange:e=>t({nb_newsletter_subject:e})}),(0,l.createElement)(s.TextControl,{label:(0,a.__)("Preview Text","wp-newsletter-builder"),placeholder:(0,a.__)("Enter preview text","wp-newsletter-builder"),value:o,onChange:e=>t({nb_newsletter_preview:e})}),f.length>0?(0,l.createElement)("label",{htmlFor:"wp-newsletter-builder-list"},(0,a.__)("Email List","wp-newsletter-builder"),(0,l.createElement)(A,{labelledBy:(0,a.__)("List","wp-newsletter-builder"),value:g,options:w,onChange:e=>{const r=e.map((e=>e.value));t({nb_newsletter_list:r})},hasSelectAll:!1,overrideStrings:{selectSomeItems:(0,a.__)("Select Email List","wp-newsletter-builder")}})):(0,l.createElement)(s.Spinner,null))},title:B.title})},251:function(e,t,r){var n=r(196),l=Symbol.for("react.element"),a=Symbol.for("react.fragment"),s=Object.prototype.hasOwnProperty,o=n.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,i={key:!0,ref:!0,__self:!0,__source:!0};function c(e,t,r){var n,a={},c=null,d=null;for(n in void 0!==r&&(c=""+r),void 0!==t.key&&(c=""+t.key),void 0!==t.ref&&(d=t.ref),t)s.call(t,n)&&!i.hasOwnProperty(n)&&(a[n]=t[n]);if(e&&e.defaultProps)for(n in t=e.defaultProps)void 0===a[n]&&(a[n]=t[n]);return{$$typeof:l,type:e,key:c,ref:d,props:a,_owner:o.current}}t.Fragment=a,t.jsx=c,t.jsxs=c},893:function(e,t,r){e.exports=r(251)},196:function(e){e.exports=window.React}},r={};function n(e){var l=r[e];if(void 0!==l)return l.exports;var a=r[e]={exports:{}};return t[e](a,a.exports,n),a.exports}n.m=t,e=[],n.O=function(t,r,l,a){if(!r){var s=1/0;for(d=0;d<e.length;d++){r=e[d][0],l=e[d][1],a=e[d][2];for(var o=!0,i=0;i<r.length;i++)(!1&a||s>=a)&&Object.keys(n.O).every((function(e){return n.O[e](r[i])}))?r.splice(i--,1):(o=!1,a<s&&(s=a));if(o){e.splice(d--,1);var c=l();void 0!==c&&(t=c)}}return t}a=a||0;for(var d=e.length;d>0&&e[d-1][2]>a;d--)e[d]=e[d-1];e[d]=[r,l,a]},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,{a:t}),t},n.d=function(e,t){for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={646:0,165:0};n.O.j=function(t){return 0===e[t]};var t=function(t,r){var l,a,s=r[0],o=r[1],i=r[2],c=0;if(s.some((function(t){return 0!==e[t]}))){for(l in o)n.o(o,l)&&(n.m[l]=o[l]);if(i)var d=i(n)}for(t&&t(r);c<s.length;c++)a=s[c],n.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return n.O(d)},r=self.webpackChunkwp_newsletter_builder=self.webpackChunkwp_newsletter_builder||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))}();var l=n.O(void 0,[165],(function(){return n(823)}));l=n.O(l)}();