document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('#replyForm');

    const input = document.querySelector('#messageInput');

    const button = document.querySelector('#sendButton');

    const messages = document.querySelector('#messages');


    function scrollToBottom() {

        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }

    }


    function escapeHtml(text) {

        const div = document.createElement('div');

        div.textContent = text ?? '';

        return div.innerHTML;

    }


    function renderMessages(messageList) {

        if (!messages) {
            return;
        }


        messages.innerHTML = '';


        messageList.forEach(message => {


            messages.insertAdjacentHTML(
                'beforeend',
                `

                <div class="message-row ${message.direction}">

                    <div class="bubble">

                        ${escapeHtml(message.message)}

                        <div class="time">

                            ${message.created_at}

                        </div>

                    </div>

                </div>

                `
            );


        });


        scrollToBottom();

    }



    /*
    |--------------------------------------------------------------------------
    | Initial Scroll
    |--------------------------------------------------------------------------
    */

    scrollToBottom();



    /*
    |--------------------------------------------------------------------------
    | Send Message AJAX
    |--------------------------------------------------------------------------
    */

    if (form) {


        form.addEventListener('submit', async function (e) {


            e.preventDefault();


            const message = input.value.trim();


            if (!message) {
                return;
            }


            button.disabled = true;

            input.readOnly = true;


            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                Sending...
            `;



            try {


                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .content;



                const response = await fetch(
                    form.action,
                    {
                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': csrfToken,

                            'Accept': 'application/json'

                        },

                        body: new FormData(form)

                    }
                );



                const data = await response.json();



                if (!response.ok || !data.success) {


                    alert(
                        data.error ??
                        'Message sending failed.'
                    );


                    return;

                }



                input.value = '';

                input.focus();



                /*
                 Refresh immediately after send
                */

                await loadMessages();



            } catch(error) {


                console.error(
                    'WhatsApp send error:',
                    error
                );


                alert(
                    'Unable to send message.'
                );


            } finally {


                button.disabled = false;

                input.readOnly = false;

                button.innerHTML = 'Send';


            }


        });

    }





    /*
    |--------------------------------------------------------------------------
    | Load Messages
    |--------------------------------------------------------------------------
    */

    async function loadMessages() {


        if (!messages) {
            return;
        }


        const customerId =
            window.location.pathname.split('/').pop();



        if (!customerId) {
            return;
        }



        try {


            const response = await fetch(
                `/api/support/${customerId}/messages`
            );



            const data = await response.json();



            if (!data.success) {
                return;
            }



            renderMessages(
                data.messages
            );



        } catch(error) {


            console.error(
                'Message refresh failed:',
                error
            );


        }


    }





    /*
    |--------------------------------------------------------------------------
    | Live Incoming Message Polling
    |--------------------------------------------------------------------------
    */

    setInterval(
        loadMessages,
        5000
    );


});
