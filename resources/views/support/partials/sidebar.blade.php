<div class="sidebar">

<div class="p-3 bg-success text-white">

<h4 class="mb-0">
<i class="bi bi-whatsapp"></i>
Customers
</h4>

</div>


<div class="p-3">

<input 
type="text"
class="form-control"
placeholder="Search customer..."
>

</div>


<div class="list-group list-group-flush">


@foreach($customers as $item)

<a href="{{ route('support.show',$item->id) }}"
class="list-group-item list-group-item-action
{{ isset($customer) && $customer->id == $item->id ? 'active' : '' }}">


<div class="fw-bold">

{{ $item->first_name ?: 'WhatsApp User' }}

</div>


<small>

{{ $item->phone }}

</small>


@if($item->messages->last())

<div class="text-muted small mt-1">

{{ Str::limit($item->messages->last()->message,35) }}

</div>

@endif


</a>

@endforeach


</div>

</div>