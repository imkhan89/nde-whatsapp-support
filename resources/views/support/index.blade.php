document.addEventListener("DOMContentLoaded", function () {

    console.log("NDE WhatsApp Support JS Loaded");


    const customerId = window.supportCustomerId;


    const form = document.getElementById("replyForm");
    const input = document.getElementById("messageInput");
    const button = document.getElementById("sendButton");
    const messagesBox = document.getElementById("messages");


    if (!messagesBox) {
        return;
    }



    function scrollBottom(){

        messagesBox.scrollTop =
            messagesBox.scrollHeight;

    }


    scrollBottom();



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


                button.disabled=true;
                button.innerText="Sending...";


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


                        input.value="";

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

                    console.error(error);

                    alert(
                        "Unable to send message"
                    );

                }


                button.disabled=false;
                button.innerText="Send";


            }
        );

    }




    /*
    |--------------------------------------------------------------------------
    | Auto Refresh Incoming Messages
    |--------------------------------------------------------------------------
    */


    let lastMessageId = 0;


    setInterval(
        async function(){


            if(!customerId){
                return;
            }



            try{


                let response =
                    await fetch(
                        `/api/support/${customerId}/messages`,
                        {
                            headers:{
                                "Accept":
                                "application/json"
                            }
                        }
                    );


                let data =
                    await response.json();



                if(
                    data.success &&
                    data.messages.length
                ){


                    data.messages.forEach(
                        function(message){


                            if(
                                message.id >
                                lastMessageId
                            ){


                                if(
                                    !document
                                    .querySelector(
                                    `[data-message-id="${message.id}"]`
                                    )
                                ){

                                    addMessage(message);

                                }


                                lastMessageId =
                                    message.id;


                            }


                        }
                    );


                    scrollBottom();

                }



            }
            catch(error){

                console.error(
                    "Polling error:",
                    error
                );

            }


        },
        5000
    );






    /*
    |--------------------------------------------------------------------------
    | Add Message
    |--------------------------------------------------------------------------
    */


    function addMessage(message){


        let row =
            document.createElement("div");


        row.className =
            "message-row " +
            message.direction;


        row.dataset.messageId =
            message.id;



        row.innerHTML = `

        <div class="bubble">

            ${escapeHtml(message.message)}

            <div class="time">

                ${message.created_at}

            </div>

        </div>

        `;


        messagesBox.appendChild(row);


    }




    function escapeHtml(text){

        let div =
            document.createElement("div");

        div.innerText=text;

        return div.innerHTML;

    }



});