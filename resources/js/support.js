document.addEventListener("DOMContentLoaded", function () {


    const form = document.getElementById("replyForm");

    const input = document.getElementById("messageInput");

    const button = document.getElementById("sendButton");

    const messagesBox = document.getElementById("messages");


    if (!form) {
        return;
    }



    /*
    |--------------------------------------------------------------------------
    | Scroll chat to bottom
    |--------------------------------------------------------------------------
    */

    function scrollBottom(){

        if(messagesBox){

            messagesBox.scrollTop = messagesBox.scrollHeight;

        }

    }


    scrollBottom();




    /*
    |--------------------------------------------------------------------------
    | AJAX Send Message
    |--------------------------------------------------------------------------
    */


    form.addEventListener("submit", async function(e){


        e.preventDefault();


        let message = input.value.trim();


        if(!message){
            return;
        }



        button.disabled = true;

        button.innerText = "Sending...";



        let formData = new FormData();

        formData.append(
            "message",
            message
        );



        try {


            let response = await fetch(
                form.action,
                {

                    method:"POST",

                    headers:{

                        "X-CSRF-TOKEN":
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,

                        "Accept":"application/json"

                    },

                    body:formData

                }
            );



            let data = await response.json();



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


    });






    /*
    |--------------------------------------------------------------------------
    | Add outgoing message instantly
    |--------------------------------------------------------------------------
    */


    function addMessage(message){


        let row=document.createElement("div");

        row.className =
            "message-row outgoing";



        row.innerHTML = `

            <div class="bubble">

                ${escapeHtml(message.text)}

                <div class="time">

                    ${message.created_at}

                    <span class="status sent">
                        ✓
                    </span>

                </div>

            </div>

        `;



        messagesBox.appendChild(row);


    }






    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */


    function escapeHtml(text){

        let div=document.createElement("div");

        div.innerText=text;

        return div.innerHTML;

    }



});