@extends("main_template")

@section('title', 'WebServer')

@section("navbar")
    <nav class="navbar navbar-expand-lg navbar-custom">
        <!--<img class="img" style="width:2%; display: block; height:2%" src="img/Icon.png">-->
        <a class="navbar-brand" href=""> Team1 Webserver - Results for</a>
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
    <div style="text-align:center;margin-left: 30px;">
        <div class="tab" style="max-width:98%">
            <button class="tablinks active" onclick="openCity(event, 'London')">Phylogenetic Trees</button>
            <button class="tablinks" onclick="openCity(event, 'Paris')">Heatmaps</button>
            <button class="tablinks" onclick="openCity(event, 'Tokyo')">Time/Location</button>
            {{--<button class="tablinks" id="download"></button>--}}
            {{--<button class="tablinks" id="download" onclick="location.href='{{url('FileManager/ready')}}'"></button>--}}
        </div>


        <div id="London" class="tabcontent" style="display: block;">
            <div class="container" style="max-width:90%">
                <div class="row">
                    <div class="column">
                        <div style="margin-top:50px;margin-left:50px;text-align: left;">
                            <label id="show-length"><input type="checkbox"> Show branch length</label><br>
                            <input type="radio" name="gender" value="0" checked> Source Site
                            <input type="radio" name="gender" value="1"> Source Type
                            <input type="radio" name="gender" value="2"> State
                        </div>
                        <div id="main" class="container" style="margin-top:40px">
                        </div>
                        <script type="text/javascript" src="{{ URL::asset('js/StackedPlot.js') }}"></script>
                    </div>
                    <div class="column">
                        <div id="drop" style="max-height:540px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="Paris" class="tabcontent">
            <div class="container" style="max-width:90%;/* padding-top: 30px; */">
                <div class="row">
                    <div class="column" style="width:50%; text-align:center">
                        <div id="my_dataviz"></div>
                    </div>
                    <div class="column" style="width:50%; text-align:center">
                        <div id="Virulence"></div>
                    </div>
					<script type="text/javascript" src="{{ URL::asset('js/Heatmap.js') }}"></script>
                </div>
            </div>
        </div>

        <div id="Tokyo" class="tabcontent">
            <div class="container" style="max-width:95%">
                <div class="row" style="padding-top:40px">
                    <div class="column" style="width:50%; text-align:center">
                    <h4 style="padding-top:5px">Isolate Timeline</h4>
                    <div id="metric-modal"></div>
                </div>
                <div class="column" style="width:50%; text-align:center">
                    <h4 style="padding-top:5px">Isolate Distribution</h4>
                    <div id="Choropleth"></div>
                </div>
            </div>
            <script type="text/javascript" src="{{ URL::asset('js/d3-tip.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>
            <script type="text/javascript" src="{{ URL::asset('js/Timeline.js') }}"></script>
            <script type="text/javascript" src="{{ URL::asset('js/choropleth.js') }}"></script>
            <link href="{{ asset('css/choropleth.css') }}" rel="stylesheet" type="text/css">
            <link href="{{ asset('css/Timeline.css') }}" rel="stylesheet" type="text/css">
        </div>
    </div>
    </div>

    <script>
        function download() {
        }
    </script>
@endsection
@section("main_container2")
@endsection
@section("footer")
@endsection