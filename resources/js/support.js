/*
|--------------------------------------------------------------------------
| NDE WhatsApp Support
| Production Realtime Chat Handler
|--------------------------------------------------------------------------
*/


if (window.NDE_SUPPORT_INITIALIZED) {

    console.log(
        "NDE Support JS already initialized"
    );


} else {


window.NDE_SUPPORT_INITIALIZED = true;



document.addEventListener(
"DOMContentLoaded",
function(){


console.log(
    "NDE WhatsApp Support JS Loaded"
);





const customerId =
    window.supportCustomerId;



const form =
    document.getElementById(
        "replyForm"
    );


const input =
    document.getElementById(
        "messageInput"
    );


const button =
    document.getElementById(
        "sendButton"
    );


const messagesBox =
    document.getElementById(
        "messages"
    );



if(!messagesBox){

    return;

}





/*
|--------------------------------------------------------------------------
| Scroll chat bottom
|--------------------------------------------------------------------------
*/


function scrollBottom(){

    messagesBox.scrollTop =
        messagesBox.scrollHeight;

}


scrollBottom();






/*
|--------------------------------------------------------------------------
| Message Tracking
|--------------------------------------------------------------------------
*/


let latestMessageId =
    Number(
        messagesBox.dataset.lastMessageId || 0
    );


let pollingRunning = false;






/*
|--------------------------------------------------------------------------
| Send Message
|--------------------------------------------------------------------------
*/


if(form){


form.addEventListener(
"submit",
async function(e){


e.preventDefault();



let message =
    input.value.trim();



if(!message){

    return;

}




button.disabled = true;

button.innerText =
    "Sending...";





let formData =
    new FormData();


formData.append(
    "message",
    message
);





try{


let response =
    await fetch(
        form.action,
        {


        method:"POST",


        headers:{


            "X-CSRF-TOKEN":
            document
            .querySelector(
                'meta[name="csrf-token"]'
            )
            .content,


            "Accept":
            "application/json"


        },


        body:formData


        }
    );





let data =
    await response.json();






if(data.success){



    addMessage(
        data.message
    );



    latestMessageId =
        Math.max(
            latestMessageId,
            Number(data.message.id)
        );



    input.value = "";



    scrollBottom();



}
else{


    alert(
        data.error ??
        "Message failed"
    );


}





}
catch(error){


console.error(
    "Send error:",
    error
);


alert(
    "Unable to send message"
);


}




button.disabled=false;

button.innerText="Send";



});


}








/*
|--------------------------------------------------------------------------
| Load New Messages
|--------------------------------------------------------------------------
*/


async function loadMessages(){



if(
    !customerId ||
    pollingRunning
){

    return;

}



pollingRunning = true;




try{



let response =
    await fetch(
        `/api/support/${customerId}/messages?_=${Date.now()}`,
        {


        method:"GET",


        headers:{


            "Accept":
            "application/json",


            "Cache-Control":
            "no-cache"


        }


        }
    );





let data =
    await response.json();






if(
    data.success &&
    Array.isArray(data.messages)
){



data.messages.forEach(
function(message){



if(
    Number(message.id)
    >
    latestMessageId
){



if(
    !document.querySelector(
        `[data-message-id="${message.id}"]`
    )
){


    addMessage(
        message
    );


}



latestMessageId =
    Number(message.id);



}



});


scrollBottom();


}




}
catch(error){


console.error(
    "Polling error:",
    error
);


}



pollingRunning=false;



}








/*
|--------------------------------------------------------------------------
| Start ONE polling timer
|--------------------------------------------------------------------------
*/


if(
    !window.NDE_SUPPORT_TIMER
){


window.NDE_SUPPORT_TIMER =
    setInterval(
        loadMessages,
        5000
    );


}









/*
|--------------------------------------------------------------------------
| Add Message To Chat
|--------------------------------------------------------------------------
*/


function addMessage(message){



let row =
    document.createElement(
        "div"
    );



row.className =
    "message-row " +
    message.direction;



row.dataset.messageId =
    message.id;





row.innerHTML = `


<div class="bubble">


${escapeHtml(
    message.message
)}



<div class="time">

${message.created_at}

</div>



</div>


`;



messagesBox.appendChild(
    row
);



}









/*
|--------------------------------------------------------------------------
| Escape HTML
|--------------------------------------------------------------------------
*/


function escapeHtml(text){


let div =
    document.createElement(
        "div"
    );


div.innerText =
    text ?? "";


return div.innerHTML;


}









/*
|--------------------------------------------------------------------------
| Cleanup Timer
|--------------------------------------------------------------------------
*/


window.addEventListener(
"beforeunload",
function(){


if(
    window.NDE_SUPPORT_TIMER
){


clearInterval(
    window.NDE_SUPPORT_TIMER
);


window.NDE_SUPPORT_TIMER =
    null;


}



});


});

}