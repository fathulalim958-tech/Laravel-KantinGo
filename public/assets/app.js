(() => {
'use strict';
if(document.body.classList.contains('login-page')){
 if('scrollRestoration' in history)history.scrollRestoration='manual';
 const resetLoginScroll=()=>window.scrollTo(0,0);
 window.addEventListener('pageshow',()=>window.setTimeout(resetLoginScroll,0),{once:true});
 window.setTimeout(resetLoginScroll,120);
}
const toggle=document.querySelector('.sidebar-toggle-btn');
const desktopMedia=window.matchMedia('(min-width:1000px)');
if(desktopMedia.matches&&toggle)document.body.classList.add('nav-open');
const syncNavToggle=()=>{
 if(!toggle)return;
 const open=document.body.classList.contains('nav-open');
 toggle.setAttribute('aria-expanded',String(open));
 toggle.setAttribute('aria-label',open?'Tutup sidebar':'Buka sidebar');
 toggle.title=open?'Tutup sidebar':'Buka sidebar';
 toggle.innerHTML=open
  ?'<svg class="fa-arrow-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5m7 7-7-7 7-7"/></svg>'
  :'<svg class="fa-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>';
};
const closeNav=()=>{document.body.classList.remove('nav-open');syncNavToggle();};
const setInitialNav=()=>syncNavToggle();
setInitialNav();
toggle?.addEventListener('click',()=>{document.body.classList.toggle('nav-open');syncNavToggle();});
document.querySelector('.sidebar-shade')?.addEventListener('click',closeNav);
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeNav();});
const splash=document.getElementById('splash-overlay');
if(splash){
 window.setTimeout(()=>{
  splash.classList.add('is-hidden');
  splash.addEventListener('transitionend',()=>splash.remove(),{once:true});
 },1500);
}
const dialog=document.getElementById('confirm-dialog');let pending=null;const approved=new WeakSet();
document.addEventListener('submit',e=>{
 const form=e.target;if(!(form instanceof HTMLFormElement)||!form.dataset.confirm||approved.has(form))return;
 e.preventDefault();pending=form;
 if(dialog?.showModal){document.getElementById('confirm-text').textContent=form.dataset.confirm;dialog.showModal();}
 else if(window.confirm(form.dataset.confirm)){approved.add(form);form.requestSubmit();}
});
document.querySelector('[data-close-dialog]')?.addEventListener('click',()=>{dialog.close();pending=null;});
document.getElementById('confirm-yes')?.addEventListener('click',()=>{if(pending){approved.add(pending);const form=pending;pending=null;dialog.close();form.requestSubmit();}});
dialog?.addEventListener('cancel',()=>{pending=null;});
const formatMoney=value=>new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(value).replace('Rp','Rp');
function animateCounters(root=document,shouldAnimate=true){
 root.querySelectorAll('[data-counter]').forEach(element=>{
  const target=Number(element.dataset.counter)||0;
  const money=element.dataset.money==='true';
  const reduced=window.matchMedia('(prefers-reduced-motion:reduce)').matches;
  if(!shouldAnimate||reduced){element.textContent=money?formatMoney(target):String(target);return;}
  const duration=850;
  const start=performance.now();
  const step=time=>{
   const progress=Math.min((time-start)/duration,1);
   const eased=1-Math.pow(1-progress,3);
   const value=Math.round(target*eased);
   element.textContent=money?formatMoney(value):String(value);
   if(progress<1)requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
 });
}
animateCounters();
const upload=document.querySelector('input[name="gambar"]');let previewURL=null;
upload?.addEventListener('change',()=>{const file=upload.files[0];if(!file)return;if(file.size>2097152){alert('Gambar maksimal 2 MB.');upload.value='';return;}if(previewURL)URL.revokeObjectURL(previewURL);previewURL=URL.createObjectURL(file);const img=document.querySelector('[data-image-preview]');if(img)img.src=previewURL;});
const live=document.querySelector('[data-live]');const pub=document.querySelector('[data-public-order]');let loading=false;
async function refresh(){
 if(loading||document.hidden||dialog?.open)return;
 const active=document.activeElement;if(active?.closest('[data-live-orders]'))return;
 if(!live&&!pub){window.location.reload();return;}
 loading=true;const label=document.querySelector('[data-live-label]');
 try{
  const params=new URLSearchParams(live?{mode:live.dataset.live,status:live.dataset.status||'',q:live.dataset.search||''}:{mode:'public',token:pub.dataset.token});
  const response=await fetch('api.php?'+params,{credentials:'same-origin',headers:{Accept:'application/json'}});
  if(response.status===401){window.location.href='login.php';return;}
  if(!response.ok)throw new Error('HTTP '+response.status);
  const data=await response.json();
  if(pub)pub.innerHTML=data.html;
  if(live){const cards=document.querySelector('[data-live-stats]');if(cards){cards.innerHTML=data.stats;cards.querySelectorAll('.stats-grid,.income-line').forEach(section=>section.classList.add('is-refreshed'));animateCounters(cards,false);}const orders=document.querySelector('[data-live-orders]');if(orders){orders.innerHTML=data.table;orders.classList.add('is-refreshed');}if(label)label.textContent='Diperbarui '+data.time+' WIB · otomatis setiap 8 detik';}
 }catch(err){if(label)label.textContent='Koneksi terputus. Mencoba kembali otomatis...';}
 finally{loading=false;}
}
document.querySelectorAll('[data-refresh]').forEach(el=>el.addEventListener('click',refresh));
if(live||pub){setInterval(refresh,8000);document.addEventListener('visibilitychange',()=>{if(!document.hidden)refresh();});}
})();
