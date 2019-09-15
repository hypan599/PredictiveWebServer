@extends("main_template")

@section('title', 'WebServer')

@section("navbar")
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="">LOKI - ecoLi OutbreaK Investigator</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".navbar-collapse"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="navbarDropdown" role="button" aria-haspopup="true"
                       aria-expanded="false" href={{url('FileManager/ready')}}>
                        File Manager
                    </a>
                </li>
            </ul>
        </div>
    </nav>
@endsection

@section("main_container")
    <div class="container-fluid">
        <div class="banner" style="width:100%; display:block;border: black medium solid;border-radius: 10px;">
            <img class="banner-img" style="width:100%; display: block; height:5%" src="img/banner.jpg">
        </div>
    </div>
@endsection

@section("main_container2")

    <div class="container" style="text-align: center;">
        <ul id="progress">
            <li class="steps">Gene Assembly</li>
            <li class="steps">Gene Prediction</li>
            <li class="steps">Gene Annotation</li>
            <li class="steps">Comparative Genomics</li>
        </ul>
        <br>
        <br>
        <br>
        <div class="container" style="text-align: justify;">
            <div class="row">
                <div class="col">
                    <div class="overlay" style="background-color: #edebeb">
                        <img class="img" style="width:100%; display: block; height:100%" src="img/gears.png">
                    </div>
                    <h5 style="font-family:verdana;text-align: center;">
                        <a href="#myModal" role="button" class="btn" data-toggle="modal">Run</a>
                    </h5>
                </div>
                <div class="col">
                    <div class="overlay" style="background-color: #edebeb">
                        <img class="img" style="width:100%; display: block; height:100%" src="img/manual.png">
                    </div>
                    <h6 style="font-family:verdana;text-align: center;"><a
                                href="https://compgenomics2019.biosci.gatech.edu/Team_I_Webserver_Group">Tutorial</a>
                    </h6>
                </div>
                <div class="col">
                    <div class="overlay" style="background-color: #edebeb">
                        <img class="img" style="width:100%; display: block; height:100%" src="img/results.png">
                    </div>
                    <h6 style="font-family:verdana;text-align:center;"><a href="javascript:ClassResults()";>Class Results</a>
                    </h6>
                </div>
                <div class="col">
                    <div class="overlay" style="background-color: #edebeb">
                        <img class="img" style="width:100%; display: block; height:100%" src="img/team.png">
                    </div>
                    <h6 style="font-family:verdana;text-align: center;"><a href={{url('about')}}>About Us</a></h6>
                </div>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Head">Analysis Pipeline</h5>
                </div>

                {{--<form class="needs-validation" method="get" action="analysis/start" novalidate>--}}
                {{--<form class="needs-validation" novalidate>--}}
                {{--IO options--}}
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationTooltip02">InputFile#1</label>
                            <select id="inputFile1" name="inputFile1" class="form-control">
                                @foreach ($files as $f)
                                    <option>{{ $f }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationTooltip02">InputFile#2</label>
                            <select id="inputFile2" name="inputFile2" class="form-control">
                                @foreach ($files as $f)
                                    <option>{{ $f }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="validationTooltip01">JobName</label>
                            <input type="text" class="form-control" name="jobName" id="jobName" placeholder="job1"
                                   required>
                        </div>
                        {{--<div class="col-md-6 mb-3">--}}
                        {{--<label for="validationTooltip01">Email</label>--}}
                        {{--<input type="text" class="form-control" name="email" id="email"--}}
                        {{--placeholder="example@gatech.edu">--}}
                        {{--</div>--}}
                    </div>
                    <div class="form-row">
                        {{--assembly options--}}
                        <div class="col-md-6 mb-3">
                            <input class="" type="checkbox" name="doAssemble" id="doAssemble" value="1"
                                   checked="checked">
                            <label class="form-check-label" for="inlineCheckbox1">Assemble</label>
                        </div>
                        {{--gene prediction options--}}
                        <div class="col-md-6 mb-3">
                            <input class="" type="checkbox" disabled name="doPrediction" id="doPrediction" value="1"
                                   checked="checked">
                            <label class="form-check-label" for="inlineCheckbox1">Gene Prediction</label>
                        </div>
                    </div>
                    {{--functional annotation options--}}
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <input class="" type="checkbox" disabled name="doAnnotation" id="doAnnotation" value="1"
                                   checked="checked">
                            <label class="form-check-label" for="inlineCheckbox1">Functional Annotation</label>
                        </div>
                    </div>
                    <div class="form-row">
                        {{--comparative genomics options--}}
                        <div class="col-md-6 mb-3">
                            <input class="" type="checkbox" disabled name="doComparative" id="doComparative" value="1"
                                   checked="checked">
                            <label class="form-check-label" for="inlineCheckbox1">Comparative Analysis</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                    {{--<button class="btn btn-success" id="play" type="submit">Run</button>--}}
                    <button class="btn btn-success" id="run2_button"
                            onClick="clickrun()">Run
                    </button>
                </div>
                {{--</form>--}}
            </div>
        </div>
    </div>
    <div id="Success" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Head">Success!</h5>
                </div>
                <div class="modal-body" style="text-align:center">
                    <h7> Follow this link for results:</h7>
                    <a style="text-align:center"
                       href={{url('output')}}>http://predict2019t1.biosci.gatech.edu/output</a>
                </div>
            </div>
        </div>
    </div>

    <div id="Error" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h7>Error! Check Inputs</h7>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clickrun() {
            document.getElementById("Head").innerHTML = "Analysis Pipeline" + '<img src="img/ajax-loader.gif" alt="Wait" />';
            console.log("function is running");
            var url = 'start_ajax';
            var f1 = $('#inputFile1').val();
            var f2 = $('#inputFile2').val();
            if ($("#doAssemble").is(':checked')) {
                var ass = 1;
            } else {
                var ass = 0;
            }
            if ($("#doPrediction").is(':checked')) {
                var pred = 1;
            } else {
                var pred = 0;
            }
            if ($("#doAnnotation").is(':checked')) {
                var anno = 1;
            } else {
                var anno = 0;
            }
            if ($("#doComparative").is(':checked')) {
                var comp = 1;
            } else {
                var comp = 0;
            }
            console.log(ass, pred, anno, comp);
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    inputFile1: f1,
                    inputFile2: f2,
                    jobName: $('#jobName').val(),
                    email: $("#email").val(),
                    doAssemble: ass,
                    doPrediction: pred,
                    doAnnotation: anno,
                    doComparative: comp

                },
                success: function (result) {
                    document.getElementById("Head").innerHTML = "Analysis Pipeline";
                    console.log(result["$tree"]);
                    $("#myModal").modal('hide');
                    $("#Success").modal('show');
                    win = window.open('{{url('output')}}');
                    setTimeout(function () {
						console.log(result)
                        win.ready(result["$tree"]);
                        win.main(d3v3.csv.parse(result["$AMR"]),d3v3.csv.parse(result["$VF"]));
                    }, 2000);
                },
                error: function (result) {
                    console.log("ajax error");
                    console.log("result", result);
                    document.getElementById("Head").innerHTML = "Analysis Pipeline";
                    $("#Error").modal('show');
                }
            });

        }
		
		function ClassResults(){
			    d3.text("life.txt", function (data) {tree=data;
					d3.csv("AMR_list.csv", function (data1) {amr=data1
						d3.csv("VFs.csv", function (data2) {vf=data2
						    win = window.open('{{url('output')}}');
							setTimeout(function () {
								win.ready(tree);
								win.main(amr,vf);
							}, 500);
						})
				  })
				})
		}

    </script>
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