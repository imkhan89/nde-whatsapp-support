<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WhatsApp Support</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,Helvetica,sans-serif;
}

body{
    background:#ece5dd;
}

.container{
    display:flex;
    height:100vh;
}

.sidebar{
    width:320px;
    background:#fff;
    border-right:1px solid #ddd;
    overflow-y:auto;
}

.sidebar h2{
    background:#25D366;
    color:#fff;
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
    background:#f7f7f7;
}

.customer.active{
    background:#e8f5e9;
}

.chat{
    flex:1;
    display:flex;
    flex-direction:column;
}

.header{
    background:#fff;
    border-bottom:1px solid #ddd;
    padding:18px;
}

.messages{
    flex:1;
    overflow-y:auto;
    padding:20px;
}

.message{
    max-width:70%;
    padding:12px;
    margin-bottom:12px;
    border-radius:8px;
    word-break:break-word;
}

.incoming{
    background:white;
    margin-right:auto;
}

.outgoing{
    background:#DCF8C6;
    margin-left:auto;
}

.time{
    margin-top:6px;
    font-size:11px;
    color:#777;
}

.empty{
    height:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#888;
    font-size:22px;
}

</style>

</head>

<body>

<div class="container">

<div class="sidebar">

<h2>Customers</h2>

@foreach($customers as $item)

<a
href="{{ route('support.show',$item->id) }}"
class="customer {{ isset($customer) && $customer->id==$item->id ? 'active' : '' }}"
>

<strong>{{ $item->first_name ?: 'WhatsApp User' }}</strong>

{{ $item->phone }}

</a>

@endforeach

</div>

<div class="chat">

@if(isset($customer))

<div class="header">

<h2>{{ $customer->first_name }}</h2>

<div>{{ $customer->phone }}</div>

</div>

<div class="messages">

@forelse($messages as $message)

<div class="message {{ $message->direction=='incoming' ? 'incoming' : 'outgoing' }}">

{{ $message->message }}

<div class="time">

{{ $message->created_at->format('d M Y H:i') }}

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