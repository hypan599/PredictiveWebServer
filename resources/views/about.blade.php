

@extends("main_template")

@section('title', 'WebServer')

@section("navbar")
    <nav class="navbar navbar-expand-lg navbar-custom">
        <!--<img class="img" style="width:2%; display: block; height:2%" src="img/Icon.png">-->
        <a class="navbar-brand" href=""> Team1 Webserver - About Us</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".navbar-collapse"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="navbarDropdown" role="button" aria-haspopup="true"
                       aria-expanded="false" href={{url('/')}}>
                        Back to Home
                    </a>
                </li>
            </ul>
        </div>
    </nav>
@endsection
@section("main_container")
    <div class="container">
        <!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
html {
  box-sizing: border-box;
}

*, *:before, *:after {
  box-sizing: inherit;
}

.column {
  float: left;
  width: 20%;
  margin-bottom: 16px;
  padding: 0 8px;
}

@media screen and (max-width: 650px) {
  .column {
    width: 100%;
    display: block;
  }
}

.card {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
}

.container {
  padding: 0 16px;
}

.container::after, .row::after {
  content: "";
  clear: both;
  display: table;
}

.title {
  color: grey;
}

.button {
  border: none;
  outline: 0;
  display: inline-block;
  padding: 8px;
  color: white;
  background-color: #000;
  text-align: center;
  cursor: pointer;
  width: 100%;
}

.button:hover {
  background-color: #555;
}
</style>
</head>
<body>

<h2></h2>
<br>

<div class="row">
  <div class="column">
    <div class="card">
      <img src="/img/arshad.png" alt="Jane" style="width:100%;height:200px">
      <div class="container">
	    <p></p>
        <h4>Zainab Arshad</h4>
        <p class="title">Team Member</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>zarshad6@gatech.edu</p>
      </div>
    </div>
  </div>
  <div class="column">
        <div class="card">
      <img src="/img/feldman.jpeg" alt="Jane" style="width:100%;height:200px">
      <div class="container">
	    <p></p>
        <h4>Jacob Feldman</h4>
        <p class="title">Team Member</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>feldman@gatech.edu</p>
      </div>
    </div>
  </div>
  <div class="column">
    <div class="card">
      <img src="/img/howard.jpeg" alt="Jane" style="width:100%;height:200px">
      <div class="container">
	    <p></p>
        <h4>Patrick Howard</h4>
        <p class="title">Team Member</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>phoward38@gatech.edu</p>
      </div>
    </div>
  </div>
  <div class="column">
    <div class="card">
      <img src="/img/pan.jpeg" alt="Jane" style="width:100%;height:200px">
      <div class="container">
	    <p></p>
        <h4>Hanying Pan</h4>
        <p class="title">Team Member</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>hpan@gatech.edu</p>
      </div>
    </div>
  </div>
    <div class="column">
     <div class="card">
      <img src="/img/sharma.jpeg" alt="Jane" style="width:100%;height:200px">
      <div class="container">
	    <p></p>
        <h4>Shrinkla Sharma</h4>
        <p class="title">Team Member</p>
        <p>Some text that describes me lorem ipsum ipsum.</p>
        <p>ssharma433@gatech.edu</p>
      </div>
    </div>
  </div>
</div>

</body>
</html>

    </div>
@endsection
@section("main_container2")
    <div class="container">
    </div>
@endsection
@section("footer")
    <div class="container-fluid" style="background-color: black; position: fixed; left: 0; bottom: 0;">
        <footer style="text-align: center;">
            <br>
            <p style="color: white;"> &copy 2019 Team 1 Predictive Webserver Group</p>
            <br>
        </footer>
    </div>
@endsection