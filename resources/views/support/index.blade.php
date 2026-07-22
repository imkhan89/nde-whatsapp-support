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
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f5f5f5;
        }

        .container{
            display:flex;
            height:100vh;
        }

        .sidebar{
            width:320px;
            background:#ffffff;
            border-right:1px solid #ddd;
            overflow-y:auto;
        }

        .sidebar h2{
            padding:20px;
            background:#25D366;
            color:white;
            font-size:22px;
        }

        .customer{
            display:block;
            padding:18px;
            color:#333;
            text-decoration:none;
            border-bottom:1px solid #eee;
        }

        .customer:hover{
            background:#f4f4f4;
        }

        .customer.active{
            background:#e8f5e9;
        }

        .customer strong{
            display:block;
            font-size:16px;
            margin-bottom:5px;
        }

        .chat{
            flex:1;
            background:#fafafa;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .welcome{
            text-align:center;
            color:#888;
        }

        .welcome h1{
            margin-bottom:15px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="sidebar">

        <h2>Customers</h2>

        @forelse($customers as $item)

            <a
                href="{{ route('support.show',$item->id) }}"
                class="customer {{ isset($customer) && $customer->id==$item->id ? 'active' : '' }}"
            >

                <strong>

                    {{ $item->first_name ?: 'WhatsApp User' }}

                </strong>

                {{ $item->phone }}

            </a>

        @empty

            <div style="padding:20px">

                No Customers Found

            </div>

        @endforelse

    </div>

    <div class="chat">

        @if(isset($customer))

            <div class="welcome">

                <h1>

                    {{ $customer->first_name }}

                </h1>

                <p>

                    {{ $customer->phone }}

                </p>

            </div>

        @else

            <div class="welcome">

                <h1>

                    WhatsApp Support Dashboard

                </h1>

                <p>

                    Select a customer from the left panel.

                </p>

            </div>

        @endif

    </div>

</div>

</body>

</html>