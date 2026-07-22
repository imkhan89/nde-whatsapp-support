document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('#replyForm');

    if (!form) {
        return;
    }


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

        div.textContent = text;

        return div.innerHTML;

    }


    scrollToBottom();


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


            const response = await fetch(form.action, {

                method: 'POST',

                headers: {

                    'X-CSRF-TOKEN': csrfToken,

                    'Accept': 'application/json'

                },

                body: new FormData(form)

            });


            const data = await response.json();



            if (!response.ok || !data.success) {


                alert(
                    data.error ?? 'Message sending failed.'
                );


                return;

            }



            const safeMessage = escapeHtml(
                data.message.text
            );


            messages.insertAdjacentHTML(
                'beforeend',
                `

                <div class="message-row outgoing">

                    <div class="bubble">

                        ${safeMessage}

                        <div class="time">

                            ${data.message.created_at}

                        </div>

                    </div>

                </div>

                `
            );


            input.value = '';

            scrollToBottom();

            input.focus();



        } catch (error) {


            console.error(
                'WhatsApp send error:',
                error
            );


            alert(
                'Unable to send message. Please try again.'
            );


        } finally {


            button.disabled = false;

            input.readOnly = false;

            button.innerHTML = 'Send';


        }


    });


});