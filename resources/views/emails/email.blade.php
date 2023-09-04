<!DOCTYPE html>
<html>
<head>
    <title>
        { !! $details['title'] !! }
    </title>
</head>
<body>
<img  style="display: none; margin:20px auto; height: 50px;">
<h1 style="text-align:center">{!!html_entity_decode($details['title'])!!}</h1>
<p>{{ $details['body'] }}</p>
@if($details['link'] != '')
    <a href="{{ $details['link']  }}">{{  $details['link_msg'] }}</a>
@endif
<p>Thank you</p>
</body>
</html>
