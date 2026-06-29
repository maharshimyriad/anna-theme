const o=document.querySelector('.transition');for(let i=0;i<10;i++){let d=document.createElement('div');d.className='block';o.appendChild(d);}
const b=[...document.querySelectorAll('.block')];
const anim=(inn)=>Promise.all(b.map((e,i)=>e.animate([{transform:`translateY(${inn?'100':'0'}%)`},{transform:`translateY(${inn?'0':'-100'}%)`}],{duration:650,delay:i*60,fill:'forwards',easing:'cubic-bezier(.77,0,.18,1)'}).finished));
async function nav(url,push=true){document.body.style.pointerEvents='none';await anim(true);let t=await fetch(url).then(r=>r.text());let d=new DOMParser().parseFromString(t,'text/html');document.title=d.title;document.querySelector('#page-content').innerHTML=d.querySelector('#page-content').innerHTML;if(push)history.pushState({},'',url);bind();window.scrollTo(0,0);await Promise.all([...document.images].map(i=>i.decode?i.decode().catch(()=>{}):Promise.resolve()));await anim(false);document.body.style.pointerEvents='';}
function bind(){document.querySelectorAll('a[data-link]').forEach(a=>a.onclick=e=>{e.preventDefault();if(a.pathname===location.pathname)return;nav(a.href);});}
window.onpopstate=()=>nav(location.pathname,false);bind();
