<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    public function index($status)
    {
        $files = Storage::Files("/");
        switch ($status) {
            case "ready":
                $prompt = "Please choose an action";
                break;

            case "fail":
                $prompt = "bad file! please check your input";
                break;

            case "pass":
                $prompt = "upload successful! please choose another action";
                break;

            case "badname":
                $prompt = "bad file name. must be fastq format.";
        }
        return view("FileManager", compact("files", "prompt"));
    }

    // todo: robust
    public function upload(Request $request)
    {
        $file = $request->file("filename");
        $category = $request->input('fileCategory')[0];
        $newFileName = $request->input('newFileName');
        $file_name_len = strlen($newFileName);
        if (substr_compare($newFileName, '.fastq', $file_name_len-6) != 0){
            return redirect("FileManager/badname");
        }
        $disk = Storage::disk('uploads');
        $disk -> putFileAs($category, $file, $newFileName);
        $output = exec('../../t1g5/bin/python3 ../scripts/filecheck.py ../storage/app/uploads/'.$category.'/'.$newFileName);
        if ($output == "fail") {
            Storage::delete($category . "/" . $newFileName);
            return redirect("FileManager/fail");
        } elseif ($output == "pass") {
            return redirect("FileManager/pass");
        } else {
            return redirect("FileManager/ready");
        }
    }

    public function get_file_list()
    {
        $files = Storage::allFiles("/");
        return view("index", compact("files"));
    }

    public function downloadOrDelete(Request $request)
    {
        $category = $request->input('fileCategory')[0];
        $fileName = $request->input('fileName');
//        dd($category);
        switch ($request->btn) {
            case "download":
//                if ()
                return Storage::download($fileName . ".zip");

            case "delete":
                if ($category == "job"){
                    Storage::delete("assemble/" . $fileName."_genome.fasta");
                    Storage::delete("assemble/" . $fileName."_quast.csv");
                    Storage::delete("prediction/" . $fileName.".fna");
                    Storage::delete("prediction/" . $fileName.".faa");
                    Storage::delete("prediction/" . $fileName.".gff");
                    Storage::delete("annotation/" . $fileName.".gff");
                    Storage::delete("comparative/" . $fileName);
                }else{
                    Storage::delete($category . "/" . $fileName);
                }
                return redirect("FileManager/ready");
        }
    }

    public function ajax_analysis(Request $request)
    {
        $input = $request->all();
        // to do: check if job name exists
        if ($request->input('inputFile1') == $request->input('inputFile2')){
            return response()->json(['error' => "error: dupin"], 404);
        }elseif (empty($request->input('inputFile1'))) {
            return response()->json(['error' => "error: noin"], 404);
        }elseif (empty($request->input('jobName'))) {
            return response()->json(['error' => "error: nojob"], 404);
        }else{
            // pass all pre check
            $base_cmd = '../../t1g5/bin/python3 ../scripts/main.py -j '.$request->input('jobName');
            if ($request->input('doAssemble') == '1'){
                $base_cmd = $base_cmd." -a";
                $input_file = " --infastq ".$request->input('inputFile1')." ".$request->input('inputFile2');
            }
            if ($request->input('doPrediction') == 1){
                $base_cmd = $base_cmd." -b";
                if (!isset($input_file)){
                    $input_file = " --infasta ".$request->input('inputFile1');
                }
            }
            if ($request->input('doAnnotation') == true){
                $base_cmd = $base_cmd." -c ";
            }
            if ($request->input('doComparative') == true){
                if (!isset($input_file)){
                    $input_file = " --infasta ".$request->input('inputFile1');
                }
                $base_cmd = $base_cmd." -d";
            }
            $base_cmd = $base_cmd.$input_file;
        }
        exec($base_cmd." 2>&1", $array, $return);
        if ($return != 0){
            return response()->json(['error' => "pipeline failed", "output" => $array], 404);
        }
        $nwk = Storage::get('comparative/'.$request->input('jobName').'newtrick.nwk');
        $vf = Storage::get('annotation/VF_list.csv');
        $amr = Storage::get('annotation/AMR_list.csv');
        return response()->json(['$tree' => $nwk,'$VF' =>$vf,'$AMR' =>$amr], 200);
    }
}
