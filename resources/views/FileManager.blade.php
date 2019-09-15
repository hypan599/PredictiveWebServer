@extends("main_template")

@section('title', 'LOKI')

@section("navbar")
    <nav class="navbar navbar-expand-lg navbar-custom">
        <p class="navbar-brand">LOKI</p>
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

@section("main_container2")
    <div class="container">
        <div>
            @foreach ($files as $f)
                <li>{!! $f !!}</li>
            @endforeach
        </div>
    </div>
@endsection

@section("main_container")
    <div class="container">
        <div class="mb-2">
            <p>{!! $prompt !!}</p>
        </div>
        <form class="form-horizontal" method="post" action="file_upload" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="file">Choose file</label>
                    <input id="file" type="file" class="form-control" name="filename" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="validationTooltip05">Category</label>
                    <select id="fileCategory" name="fileCategory[]" class="form-control">
                        <option value="assemble" selected>assemble</option>
                        <option value="prediction">prediction</option>
                        <option value="annotation">annotation</option>
                        <option value="comparative">comparative</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>New file name</label>
                    <input type="text" class="form-control" id="newFileName" name="newFileName" placeholder="target.test" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Click to upload file</label>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
    <div class="container">
        <form class="form-horizontal" method="POST" action="file_downloadOrDelete" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="file">Type in job name</label>
                    <input type="text" class="form-control" id="fileName" name="fileName" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Download Results</label>
                    <button type="submit" class="btn btn-primary" name="btn" value="download">Download</button>
                </div>
            </div>
        </form>
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



