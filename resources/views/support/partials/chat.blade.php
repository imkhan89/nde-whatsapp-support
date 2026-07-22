<div class="chat-area">


@if(isset($customer))


<div class="bg-white border-bottom p-3">

<h5 class="mb-0">

{{ $customer->first_name }}

</h5>

<small>

{{ $customer->phone }}

</small>

</div>


<div class="flex-grow-1 p-4 overflow-auto">


@foreach($messages as $message)


<div class="d-flex mb-3 
{{ $message->direction == 'outgoing' ? 'justify-content-end':'justify-content-start' }}">


<div class="p-3 rounded shadow-sm
{{ $message->direction == 'outgoing' ? 'bg-success text-white':'bg-white' }}"
style="max-width:70%">


{{ $message->message }}


<div class="small mt-1 opacity-75">

{{ $message->created_at->format('d M H:i') }}

</div>


</div>

</div>


@endforeach


</div>


@include('support.partials.composer')


@else


<div class="d-flex align-items-center justify-content-center h-100">

<h3 class="text-muted">

Select a customer

</h3>

</div>


@endif


</div>