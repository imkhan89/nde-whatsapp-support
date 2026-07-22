<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>NDE WhatsApp Support</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<style>

body{
    height:100vh;
    overflow:hidden;
    background:#f0f2f5;
}

.support-container{
    height:100vh;
    display:flex;
}

.sidebar{
    width:350px;
    background:#fff;
    border-right:1px solid #ddd;
}

.chat-area{
    flex:1;
    display:flex;
    flex-direction:column;
}


</style>

</head>


<body>

<div class="support-container">

    {{ $slot }}

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>