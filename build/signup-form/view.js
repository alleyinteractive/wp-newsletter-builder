window.addEventListener("DOMContentLoaded",(()=>{document.querySelectorAll('[data-component="newsletter-builder-signup"]').forEach((e=>{const r=e.querySelector("button");r&&r.addEventListener("click",(r=>{r.preventDefault();const t=e.querySelector('input[type="email"]'),n=e.querySelectorAll('input[type="checkbox"]:checked'),s=e.querySelector('input[name="newsletter-builder-hidden"]'),l=e.querySelector(".wp-block-newsletter-builder-signup-form__response");if(!l||!t)return;if(l.innerHTML="",l.classList.remove("success","error"),!t.value)return l.classList.add("error"),l.innerHTML="Email is required",void t.focus();if(!n.length&&!s)return l.classList.add("error"),void(l.innerHTML="Please select a newsletter");const o=new URLSearchParams;var i;o.append("email",t.value),s?o.append("listIds",s.value):o.append("listIds",(null!==(i=Array.from(n).map((e=>e.value)))&&void 0!==i?i:[]).join(",")),fetch("/wp-json/newsletter-builder/v1/subscribe",{method:"POST",body:o,headers:{"Content-Type":"application/x-www-form-urlencoded"}}).then((e=>e.json())).then((e=>{const{success:r,message:t}=e;l.classList.add(r?"success":"error"),l.innerHTML=t}))}))}))}));