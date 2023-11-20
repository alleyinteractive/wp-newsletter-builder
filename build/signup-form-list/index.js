!function(){"use strict";var e={n:function(t){var l=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(l,{a:l}),l},d:function(t,l){for(var n in l)e.o(l,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:l[n]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.blocks,l=window.React,n=window.wp.i18n,i=window.wp.data,r=window.wp.components,o=window.wp.blockEditor,a=window.wp.element,s=window.wp.apiFetch,c=e.n(s);function u(e){let{selected:t,updateFunction:i}=e;const[o,s]=(0,a.useState)([]),u=o.length>0?(e=>{const t=e.map((e=>({label:e.Name,value:e.ListID})));return t.unshift({label:(0,n.__)("Select a list","wp-newsletter-builder"),value:""}),t})(o):[];return(0,a.useEffect)((()=>{o.length>0||c()({path:"/wp-newsletter-builder/v1/lists"}).then((e=>{s(e)}))}),[o]),(0,l.createElement)(r.SelectControl,{value:t,options:u,onChange:i})}var d=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"wp-newsletter-builder/signup-form-list","version":"0.1.0","title":"Newsletter Signup Form List","category":"widgets","icon":"yes","description":"Child block of Newsletter Signup Form to show a single list","textdomain":"wp-newsletter-builder","editorScript":"file:index.ts","render":"file:render.php","attributes":{"logo":{"type":"number","default":0},"title":{"type":"string"},"frequency":{"type":"string"},"description":{"type":"string"},"listId":{"type":"string"},"initialChecked":{"type":"boolean","default":false}}}');(0,t.registerBlockType)(d,{apiVersion:2,edit:function(e){let{attributes:{logo:t=0,title:a="",frequency:s="",description:c="",listId:d="",initialChecked:p=!1},setAttributes:w}=e;const{logoMedia:m=null}=(0,i.useSelect)((e=>({logoMedia:t?e("core").getMedia(t):null})),[t]);return(0,l.createElement)("div",{...(0,o.useBlockProps)()},m?(0,l.createElement)(l.Fragment,null,(0,l.createElement)(r.Button,{type:"button",onClick:()=>w({logo:null}),"aria-label":(0,n.__)("Remove Logo","wp-newsletter-builder"),isDestructive:!0,variant:"primary",className:"wp-block-wp-newsletter-builder-signup-form-list__image_delete"},"X"),(0,l.createElement)("img",{src:m.media_details?.sizes?.medium?.source_url||m.source_url,alt:(0,n.__)("Newsletter Logo","wp-newsletter-builder")})):(0,l.createElement)(o.MediaPlaceholder,{icon:"format-image",labels:{title:(0,n.__)("Image","wp-newsletter-builder"),instructions:(0,n.__)("Drag an image, upload a new one or select a file from your library.","wp-newsletter-builder")},onSelect:e=>w({logo:e.id}),accept:"image/*",allowedTypes:["image"]}),(0,l.createElement)("div",{className:"wp-block-wp-newsletter-builder-signup-form-list__content"},(0,l.createElement)(o.RichText,{tagName:"h3",value:a,onChange:e=>w({title:e}),placeholder:(0,n.__)("Title","wp-newsletter-builder")}),(0,l.createElement)(o.RichText,{tagName:"div",value:s,className:"wp-block-wp-newsletter-builder-signup-form-list__frequency",onChange:e=>w({frequency:e}),placeholder:(0,n.__)("Frequency","wp-newsletter-builder")}),(0,l.createElement)(o.RichText,{tagName:"div",value:c,className:"wp-block-wp-newsletter-builder-signup-form-list__description",onChange:e=>w({description:e}),placeholder:(0,n.__)("Description","wp-newsletter-builder")}),(0,l.createElement)(r.CheckboxControl,{checked:p,onChange:e=>w({initialChecked:e})})),(0,l.createElement)(o.InspectorControls,null,(0,l.createElement)(r.PanelBody,{title:(0,n.__)("List Settings","wp-newsletter-builder")},(0,l.createElement)(r.PanelRow,null,(0,l.createElement)(u,{selected:d,updateFunction:e=>w({listId:e})})))))},title:d.title})}();