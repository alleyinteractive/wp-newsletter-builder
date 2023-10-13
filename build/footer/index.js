!function(){var e,t={168:function(e,t,r){"use strict";var n=window.wp.blocks,l=window.wp.element,i=window.wp.i18n,o=r(184),s=r.n(o),a=window.wp.blockEditor,c=window.wp.apiFetch,u=r.n(c),p=window.wp.components,d=window.wp.data,f=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"newsletter-builder/footer","version":"0.1.0","title":"Newsletter Footer","category":"design","icon":"align-wide","description":"Outputs the company logo, social links, address, and footer links.","textdomain":"footer","editorScript":"file:index.ts","editorStyle":"file:index.css","style":["file:style-index.css"],"render":"file:render.php","attributes":{"narrow_separator":{"type":"boolean","default":false}}}');(0,n.registerBlockType)(f,{apiVersion:2,edit:function(e){var t,r,n,o,c,f;let{attributes:{narrow_separator:w=!1}}=e;const[m,b]=(0,l.useState)(!0),[_,g]=(0,l.useState)(),h=null!==(t=_?.facebook_url)&&void 0!==t?t:"",k=null!==(r=_?.twitter_url)&&void 0!==r?r:"",v=null!==(n=_?.instagram_url)&&void 0!==n?n:"",E=null!==(o=_?.youtube_url)&&void 0!==o?o:"",y=null!==(c=_?.image)&&void 0!==c?c:0,N=null!==(f=_?.address)&&void 0!==f?f:"";(0,l.useEffect)((()=>{_?b(!1):u()({path:"/newsletter-builder/v1/footer_settings"}).then((e=>{g(e)}))}),[_]);const{media:O=null}=(0,d.useSelect)((e=>({media:y?e("core").getMedia(y):null})),[_,y]),x=O?O.source_url:"",S=O?O.alt_text:"";return(0,l.createElement)("div",(0,a.useBlockProps)(),(0,l.createElement)("hr",{className:s()("wp-block-separator","has-alpha-channel-opacity",{"is-style-wide":!w})}),m?(0,l.createElement)(p.Spinner,null):(0,l.createElement)(l.Fragment,null,h||k||v||E?(0,l.createElement)("div",{className:"wp-block-newsletter-builder-footer__social-links"},h?(0,l.createElement)("span",{className:"wp-block-newsletter-builder-footer__social-links__item"},(0,l.createElement)("a",{className:"wp-block-newsletter-builder-footer__social-links__link",href:h},(0,l.createElement)("img",{src:"/wp-content/plugins/newsletter-builder/images/facebook.png",alt:"Facebook",height:"26",width:"26"}))):null,k?(0,l.createElement)("span",{className:"wp-block-newsletter-builder-footer__social-links__item"},(0,l.createElement)("a",{className:"wp-block-newsletter-builder-footer__social-links__link",href:k},(0,l.createElement)("img",{src:"/wp-content/plugins/newsletter-builder/images/twitter.png",alt:"Twitter",height:"26",width:"26"}))):null,v?(0,l.createElement)("span",{className:"wp-block-newsletter-builder-footer__social-links__item"},(0,l.createElement)("a",{className:"wp-block-newsletter-builder-footer__social-links__link",href:v},(0,l.createElement)("img",{src:"/wp-content/plugins/newsletter-builder/images/instagram.png",alt:"Instagram",height:"26",width:"26"}))):null,E?(0,l.createElement)("span",{className:"wp-block-newsletter-builder-footer__social-links__item"},(0,l.createElement)("a",{className:"wp-block-newsletter-builder-footer__social-links__link",href:E},(0,l.createElement)("img",{src:"/wp-content/plugins/newsletter-builder/images/youtube.png",alt:"YouTube",height:"26",width:"26"}))):null):null,x?(0,l.createElement)("div",{className:"wp-block-newsletter-builder-footer__logo"},(0,l.createElement)("img",{src:x,alt:S,width:"300"})):null,N?(0,l.createElement)("div",{className:"wp-block-newsletter-builder-footer__address"},N):null),(0,l.createElement)("div",{className:"wp-block-newsletter-builder-footer__links"},(0,l.createElement)("preferences",null,(0,i.__)("Preferences","newsletter-builder"))," | ",(0,l.createElement)("unsubscribe",null,(0,i.__)("Unsubscribe","newsletter-builder"))))},title:f.title})},184:function(e,t){var r;!function(){"use strict";var n={}.hasOwnProperty;function l(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var i=typeof r;if("string"===i||"number"===i)e.push(r);else if(Array.isArray(r)){if(r.length){var o=l.apply(null,r);o&&e.push(o)}}else if("object"===i){if(r.toString!==Object.prototype.toString&&!r.toString.toString().includes("[native code]")){e.push(r.toString());continue}for(var s in r)n.call(r,s)&&r[s]&&e.push(s)}}}return e.join(" ")}e.exports?(l.default=l,e.exports=l):void 0===(r=function(){return l}.apply(t,[]))||(e.exports=r)}()}},r={};function n(e){var l=r[e];if(void 0!==l)return l.exports;var i=r[e]={exports:{}};return t[e](i,i.exports,n),i.exports}n.m=t,e=[],n.O=function(t,r,l,i){if(!r){var o=1/0;for(u=0;u<e.length;u++){r=e[u][0],l=e[u][1],i=e[u][2];for(var s=!0,a=0;a<r.length;a++)(!1&i||o>=i)&&Object.keys(n.O).every((function(e){return n.O[e](r[a])}))?r.splice(a--,1):(s=!1,i<o&&(o=i));if(s){e.splice(u--,1);var c=l();void 0!==c&&(t=c)}}return t}i=i||0;for(var u=e.length;u>0&&e[u-1][2]>i;u--)e[u]=e[u-1];e[u]=[r,l,i]},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,{a:t}),t},n.d=function(e,t){for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={264:0,880:0};n.O.j=function(t){return 0===e[t]};var t=function(t,r){var l,i,o=r[0],s=r[1],a=r[2],c=0;if(o.some((function(t){return 0!==e[t]}))){for(l in s)n.o(s,l)&&(n.m[l]=s[l]);if(a)var u=a(n)}for(t&&t(r);c<o.length;c++)i=o[c],n.o(e,i)&&e[i]&&e[i][0](),e[i]=0;return n.O(u)},r=self.webpackChunknewsletter_builder=self.webpackChunknewsletter_builder||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))}();var l=n.O(void 0,[880],(function(){return n(168)}));l=n.O(l)}();