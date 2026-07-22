<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>NDE WhatsApp Support</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    background:#ece5dd;
    font-family:Arial, Helvetica, sans-serif;
}

.container-fluid{
    height:100vh;
}

.sidebar{
    height:100vh;
    overflow-y:auto;
    border-right:1px solid #ddd;
    background:#fff;
}

.customer{
    display:block;
    padding:15px;
    border-bottom:1px solid #eee;
    text-decoration:none;
    color:#222;
}

.customer:hover{
    background:#f7f7f7;
}

.customer.active{
    background:#e8f5e9;
}

.chat-wrapper{
    height:100vh;
    display:flex;
    flex-direction:column;
}

.chat-header{
    background:#fff;
    border-bottom:1px solid #ddd;
    padding:18px;
}

.messages{
    flex:1;
    overflow-y:auto;
    padding:20px;
}

.message-row{
    display:flex;
    margin-bottom:12px;
}

.message-row.incoming{
    justify-content:flex-start;
}

.message-row.outgoing{
    justify-content:flex-end;
}

.bubble{
    max-width:70%;
    padding:12px;
    border-radius:10px;
    box-shadow:0 1px 2px rgba(0,0,0,.15);
}

.incoming .bubble{
    background:#fff;
}

.outgoing .bubble{
    background:#dcf8c6;
}

.time{
    margin-top:6px;
    font-size:11px;
    color:#777;
}

.composer{
    background:#fff;
    border-top:1px solid #ddd;
    padding:15px;
}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

<div class="col-md-3 sidebar">

<h4 class="p-3 bg-success text-white mb-0">

Customers

</h4>

@foreach($customers as $item)

<a
href="{{ route('support.show',$item->id) }}"
class="customer {{ isset($customer) && $customer->id==$item->id ? 'active' : '' }}"
>

<strong>

{{ $item->first_name ?: 'WhatsApp User' }}

</strong>

<br>

<small>

{{ $item->phone }}

</small>

</a>

@endforeach

</div>

<div class="col-md-9 p-0">

@if(isset($customer))

<div class="chat-wrapper">

<div class="chat-header">

<h5 class="mb-1">

{{ $customer->first_name ?: 'WhatsApp User' }}

</h5>

<small>

{{ $customer->phone }}

</small>

</div>

<div class="messages">

@forelse($messages as $message)

<div class="message-row {{ $message->direction }}">

<div class="bubble">

{{ $message->message }}

<div class="time">

{{ $message->created_at->format('d M Y H:i') }}

</div>

</div>

</div>

@empty

No conversation yet.

@endforelse

</div>

<div class="composer">

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<form method="POST" action="{{ route('support.reply', $customer->id) }}">
    @csrf

    <div class="input-group">

        <input
            type="text"
            name="message"
            class="form-control"
            placeholder="Type your message..."
            value="{{ old('message') }}"
            required
            maxlength="4096"
        >

        <button
            class="btn btn-success"
            type="submit"
        >
            Send
        </button>

    </div>

</form>

</div>

</div>

@else

<div
class="d-flex justify-content-center align-items-center"
style="height:100vh;"
>

<h3 class="text-muted">

Select a customer

</h3>

</div>

@endif

</div>

</div>

</div>

</body>

</html>