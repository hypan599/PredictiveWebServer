<!doctype html>
<html lang="en">
<head>
</head>
<body>
@extends("main_template")

@section('title', 'WebServer - Analysis')

@section("navbar")
    <nav class="navbar navbar-expand-lg navbar-custom">
        <!--<img class="img" style="width:2%; display: block; height:2%" src="img/Icon.png">-->
        <a class="navbar-brand" href=""> Team1 Webserver - Genome Analysis</a>
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
        <form class="needs-validation" method="get" action="start" novalidate>
            {{--IO options--}}
            <div class="form-row">
                <div class="col-md-1 mb-3">
                    <p>I/O options:</p>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="validationTooltip02">InputFile#1</label>
                    <select id="inputFile" name="inputFile1" class="form-control">
                        @foreach ($files as $f)
                        <option>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="validationTooltip02">InputFile#2</label>
                    <select id="inputFile" name="inputFile2" class="form-control">
                        @foreach ($files as $f)
                        <option>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-3">
                    <label for="validationTooltip01">JobName</label>
                    <input type="text" class="form-control" name="jobName" id="jobname" placeholder="job1" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationTooltip01">Email</label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="example@gatech.edu" required>
                </div>
            </div>
            <div class="form-row">
                {{--assembly options--}}
                <div class="col-md-6 mb-3">
                    <input class="form-check-input" type="checkbox" name="doAssemble" id="inlineCheckbox1" value="1" checked="checked">
                    <label class="form-check-label" for="inlineCheckbox1">Assemble</label>
                </div>
                {{--gene prediction options--}}
                <div class="col-md-6 mb-3">
                    <input class="form-check-input" type="checkbox" name="doPrediction" id="inlineCheckbox1" value="1" checked="checked">
                    <label class="form-check-label" for="inlineCheckbox1">Gene Prediction</label>
                </div>
            </div>
            {{--functional annotation options--}}<div class="form-row">
                <div class="col-md-2 mb-3">
                    <input class="form-check-input" type="checkbox" name="doAnnotation" id="inlineCheckbox1" value="1" checked="checked">
                    <label class="form-check-label" for="inlineCheckbox1">Functional Annotation</label>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="annotationRadio" id="inlineCheckbox" value="vfdb" checked="checked">
                        <label class="form-check-label" for="inlineCheckbox1">vfDB</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="annotationRadio" id="inlineCheckbox" value="card">
                        <label class="form-check-label" for="inlineCheckbox2">CARD</label>
                    </div>
                </div>
                {{--comparative genomics options--}}
                <div class="col-md-6 mb-3">
                    <input class="form-check-input" type="checkbox" name="doComparative" id="inlineCheckbox1" value="1" checked="checked">
                    <label class="form-check-label" for="inlineCheckbox1">Comparative Analysis</label>
                </div>
            </div>

            {{--submit--}}
            <button class="btn btn-primary" type="submit">Start</button>
        </form>
    </div>
@endsection
@section("main_container2")
    <div class="container">
        <p>{!! $prompt !!}</p>
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