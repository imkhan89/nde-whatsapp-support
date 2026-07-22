document.addEventListener(`DOMContentLoaded`,function(){console.log(`NDE WhatsApp Support JS Loaded`);let e=window.supportCustomerId,t=document.getElementById(`replyForm`),n=document.getElementById(`messageInput`),r=document.getElementById(`sendButton`),i=document.getElementById(`messages`);if(!i)return;function a(){i.scrollTop=i.scrollHeight}a(),t&&t.addEventListener(`submit`,async function(e){e.preventDefault();let i=n.value.trim();if(!i)return;r.disabled=!0,r.innerText=`Sending...`;let o=new FormData;o.append(`message`,i);try{let e=await(await fetch(t.action,{method:`POST`,headers:{"X-CSRF-TOKEN":document.querySelector(`meta[name="csrf-token"]`).content,Accept:`application/json`},body:o})).json();e.success?(s(e.message),n.value=``,a()):alert(e.error??`Message failed`)}catch(e){console.error(e),alert(`Unable to send message`)}r.disabled=!1,r.innerText=`Send`});let o=0;setInterval(async function(){if(e)try{let t=await(await fetch(`/api/support/${e}/messages`,{headers:{Accept:`application/json`}})).json();t.success&&t.messages.length&&(t.messages.forEach(function(e){e.id>o&&(document.querySelector(`[data-message-id="${e.id}"]`)||s(e),o=e.id)}),a())}catch(e){console.error(`Polling error:`,e)}},5e3);function s(e){let t=document.createElement(`div`);t.className=`message-row `+e.direction,t.dataset.messageId=e.id,t.innerHTML=`

            <div class="bubble">

                ${c(e.message)}

                <div class="time">

                    ${e.created_at}

                </div>

            </div>

        `,i.appendChild(t)}function c(e){let t=document.createElement(`div`);return t.innerText=e,t.innerHTML}});