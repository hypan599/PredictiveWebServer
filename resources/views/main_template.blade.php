<!doctype html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="img/Icon.png"/>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Team1 - @yield('title')</title>
    <link href="{{ asset('css/Home.css') }}" rel="stylesheet" type="text/css" >
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://d3js.org/d3.v4.min.js"></script>
    <script type="text/javascript" src="js/d3.v3.js"></script>
    <link href="{{ asset('css/StackedPlot.css') }}" rel="stylesheet" type="text/css" >
</head>
<body>

@section("navbar")
    this is navbar
@show

<br>

@section("main_container")
    this is main1
@show
<hr>

@section("main_container2")
    this is main2
@show

@section("footer")
    this is footer
@show

</body>
</html>