<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>NDE WhatsApp Support</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<style>

body{
    margin:0;
    height:100vh;
    overflow:hidden;
    background:#f0f2f5;
    font-family:Arial, Helvetica, sans-serif;
}


.support-container{

    display:flex;
    height:100vh;

}


/* Sidebar */

.sidebar{

    width:350px;
    background:white;
    border-right:1px solid #ddd;
    overflow-y:auto;

}


.sidebar-header{

    background:#25D366;
    color:white;
    padding:20px;

}


.customer{

    display:block;
    padding:15px;
    text-decoration:none;
    color:#222;
    border-bottom:1px solid #eee;

}


.customer:hover{

    background:#f5f5f5;

}


.customer.active{

    background:#e8f5e9;

}


.customer-name{

    font-weight:bold;

}


.customer-phone{

    font-size:13px;
    color:#666;

}


.last-message{

    font-size:13px;
    color:#777;
    margin-top:5px;

}



/* Chat */

.chat{

    flex:1;
    display:flex;
    flex-direction:column;

}


.chat-header{

    background:white;
    padding:18px;
    border-bottom:1px solid #ddd;

}


.messages{

    flex:1;
    overflow-y:auto;
    padding:25px;

}



/* Messages */


.message-wrapper{

    display:flex;
    margin-bottom:15px;

}


.message-wrapper.incoming{

    justify-content:flex-start;

}


.message-wrapper.outgoing{

    justify-content:flex-end;

}



.message{

    max-width:70%;
    padding:12px 15px;
    border-radius:10px;
    box-shadow:0 1px 2px rgba(0,0,0,.1);

}



.incoming .message{

    background:white;

}


.outgoing .message{

    background:#dcf8c6;

}



.time{

    font-size:11px;
    color:#777;
    margin-top:6px;

}



/* Empty */

.empty{

    height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#777;
    font-size:22px;

}


</style>

</head>


<body>


<div class="support-container">


<!-- Sidebar -->

<div class="sidebar">


<div class="sidebar-header">

<h4 class="mb-0">

<i class="bi bi-whatsapp"></i>

Customers

</h4>

</div>



<div class="p-3">

<input 
type="text"
class="form-control"
placeholder="Search customers..."
>

</div>



@forelse($customers as $item)


<a
href="{{ route('support.show',$item->id) }}"
class="customer {{ isset($customer) && $customer->id == $item->id ? 'active' : '' }}"
>


<div class="customer-name">

{{ $item->first_name ?: 'WhatsApp User' }}

</div>


<div class="customer-phone">

{{ $item->phone }}

</div>


@if($item->messages->count())

<div class="last-message">

{{ Str::limit($item->messages->last()->message,40) }}

</div>

@endif


</a>


@empty


<div class="p-3 text-muted">

No customers found.

</div>


@endforelse



</div>



<!-- Chat Area -->


<div class="chat">


@if(isset($customer))


<div class="chat-header">


<h5 class="mb-1">

{{ $customer->first_name ?: 'WhatsApp User' }}

</h5>


<div class="text-muted">

{{ $customer->phone }}

</div>


</div>



<div class="messages">


@forelse($messages as $message)


<div class="message-wrapper {{ $message->direction }}">


<div class="message">


<div>

{{ $message->message }}

</div>


<div class="time">

{{ $message->created_at->format('d M Y H:i') }}

</div>


</div>


</div>



@empty


<div class="empty">

No conversation yet.

</div>


@endforelse



</div>



@else


<div class="empty">

Select a customer

</div>


@endif



</div>


</div>


</body>

</html>