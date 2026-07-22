window.NDE_SUPPORT_INITIALIZED?console.log(`NDE Support JS already initialized`):(window.NDE_SUPPORT_INITIALIZED=!0,document.addEventListener(`DOMContentLoaded`,function(){console.log(`NDE WhatsApp Support JS Loaded`);let e=window.supportCustomerId,t=document.getElementById(`replyForm`),n=document.getElementById(`messageInput`),r=document.getElementById(`sendButton`),i=document.getElementById(`messages`);if(!i)return;function a(){i.scrollTop=i.scrollHeight}a();let o=Number(i.dataset.lastMessageId||0),s=!1;t&&t.addEventListener(`submit`,async function(e){e.preventDefault();let i=n.value.trim();if(!i)return;r.disabled=!0,r.innerText=`Sending...`;let s=new FormData;s.append(`message`,i);try{let e=await(await fetch(t.action,{method:`POST`,headers:{"X-CSRF-TOKEN":document.querySelector(`meta[name="csrf-token"]`).content,Accept:`application/json`},body:s})).json();e.success?(l(e.message),o=Math.max(o,Number(e.message.id)),n.value=``,a()):alert(e.error??`Message failed`)}catch(e){console.error(`Send error:`,e),alert(`Unable to send message`)}r.disabled=!1,r.innerText=`Send`});async function c(){if(!(!e||s)){s=!0;try{let t=await(await fetch(`/api/support/${e}/messages?_=${Date.now()}`,{method:`GET`,headers:{Accept:`application/json`,"Cache-Control":`no-cache`}})).json();t.success&&Array.isArray(t.messages)&&(t.messages.forEach(function(e){Number(e.id)>o&&(document.querySelector(`[data-message-id="${e.id}"]`)||l(e),o=Number(e.id))}),a())}catch(e){console.error(`Polling error:`,e)}s=!1}}window.NDE_SUPPORT_TIMER||(window.NDE_SUPPORT_TIMER=setInterval(c,5e3));function l(e){let t=document.createElement(`div`);t.className=`message-row `+e.direction,t.dataset.messageId=e.id,t.innerHTML=`


<div class="bubble">


${u(e.message)}



<div class="time">

${e.created_at}

</div>



</div>


`,i.appendChild(t)}function u(e){let t=document.createElement(`div`);return t.innerText=e??``,t.innerHTML}window.addEventListener(`beforeunload`,function(){window.NDE_SUPPORT_TIMER&&(clearInterval(window.NDE_SUPPORT_TIMER),window.NDE_SUPPORT_TIMER=null)})}));