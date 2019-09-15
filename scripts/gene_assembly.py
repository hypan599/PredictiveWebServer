#python3  geneassembly_pipeline_t1.py -i ../Data/CGT1020_1.fq.gz ../Data/CGT1020_2.fq.gz -o assembly -a spades


import os
import sys
import shutil
import argparse
import subprocess
import pandas as pd
import numpy as np


def assemble_genomes(_tmp_dir, _assemblers, _threads, _out_name, _seq_len):
    """
    run different assemblers and choose the best result
    :param _tmp_dir: tmp directory
    :param _assemblers: assemblers given by user
    :return: None
    """
    quast_command = []
    
    run_spades(_tmp_dir, _seq_len)
    
    subprocess.call(["quast-5.0.2/quast.py",  _tmp_dir + "/spades/scaffolds.fasta", "-t", str(_threads), "-o", _tmp_dir + "/quast"])
    quast_result = "%s/quast/report.tsv" % _tmp_dir
    
    try:
       result = pd.read_table(quast_result, index_col=0)
    except FileNotFoundError:
       print("quast report does not exist!")
       print("Abort")
       return
    result.loc["score"] = np.log(result.loc["Total length (>= 0 bp)"] * result.loc["N50"] / result.loc["# contigs"])
    result.to_csv("final_quast.csv", header=True, index=True)
    print("-" * 20 + "quast finished" + "-" * 20)
    subprocess.call(["mv", _tmp_dir + "/spades/scaffolds.fasta", _out_name])


def run_spades(_tmp_dir, _fastqc_dir):
    """
    run spades on trimmed file
    :param _tmp_dir: tmp directory
    :return: output contigs file name
    """
    spades_cmd = ["SPAdes-3.11.1-Linux/bin/spades.py", "--phred-offset", "33", "-1", "{0}/trimmed_1P.fastq".format(_tmp_dir), "-2", "{0}/trimmed_2P.fastq".format(_tmp_dir), "-o", "{0}/spades".format(_tmp_dir)]
    if "trimmed_U.fastq" in os.listdir(_tmp_dir):
        spades_cmd.extend(["-s", "{0}/trimmed_U.fastq".format(_tmp_dir)])
    subprocess.call(spades_cmd)


def run_fake_trim(trimmomatic_jar, _input_files, _tmp_dir, _threads):
    """
    generate fastq file for assembler, do not change file content
    :param trimmomatic_jar: trimmomatic jar file
    :param _input_files: input fastq file
    :param _tmp_dir: tmp directory
    :return: None
    """
    command = ["java", "-jar", trimmomatic_jar, "PE", "-threads", str(_threads), _input_files[0], _input_files[1], "-baseout", _tmp_dir + "/trimmed.fastq", "MINLEN:100"]
    # print("fake", command)
    subprocess.call(command)
    subprocess.call(["rm", "-rf", "{0}/trimmed_*U.fastq".format(_tmp_dir)])


def run_fastqc(_input_file, _tmp_dir):
    """
    run fastqc for each file
    :param _input_file: input fastq file
    :param _tmp_dir: tmp directory
    :return: None
    """
    subprocess.call(["FastQC/fastqc", "--extract", "-t", "15", "-o", _tmp_dir, _input_file], stderr=subprocess.DEVNULL)


def check_crop(_tmp_dir, _fastqc_dirs):
    """
    check if hard crop is needed
    :param _tmp_dir: tmp directory
    :param _fastqc_dirs: location of fastqc report
    :return: [head_crop, crop]
    """
    crops = [0, 0]
    for i, dir in enumerate(_fastqc_dirs):
        qualities = []
        positions = []
        data_file_name = "%s/%s/fastqc_data.txt" % (_tmp_dir, dir)
        with open(data_file_name, "r") as data:
            data_recorded = False
            for line in data:
                if line.startswith("#"):
                    continue
                elif "Per base sequence quality" in line:
                    data_recorded = True
                elif data_recorded and line.startswith(">>"):
                    break
                elif data_recorded:
                    position, quality, *tmp = line.split()
                    positions.append(position)
                    qualities.append(float(quality))
                continue
        i = 0
        while qualities[i] < 20:
            i += 1
        crops[0] = max(int(positions[i].split("-")[-1]), crops[0])
        i = len(qualities) - 1
        while qualities[i] < 20:
            i -= 1
        crops[1] = min(int(positions[i].split("-")[0]), crops[1])
    return crops


def run_trim(trimmomatic_jar, _input_files, _tmp_dir, _threads, _skip_crop, window, threshold, headcrop, crop):
    """
    run trimmomatic on fastq file
    :param trimmomatic_jar: trimmomatic jar file
    :param _input_files: input fastq file
    :param _tmp_dir: tmp directory
    :param window: windown size of sliding window
    :param threshold: threshold in each window
    :param headcrop: hard crop from beginning
    :param crop: hard crop from end
    :return: drop rate of this trimming
    """
    command = ["java", "-jar", trimmomatic_jar, "PE", "-threads", str(_threads), _input_files[0], _input_files[1], "-baseout", _tmp_dir + "/trimmed.fastq"]
    if not _skip_crop:
        if headcrop:
            command.append("HEADCROP:%d" % headcrop)
        if crop:
            command.append("CROP:%d" % crop)
    command.extend(["SLIDINGWINDOW:%d:%d" % (window, threshold), "MINLEN:100"])
    proc = subprocess.Popen(command, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    proc.wait()
    out, trim_summary = proc.communicate()
    if trim_summary:
        trim_summary = trim_summary.decode("utf-8")
        drop_rate = trim_summary.split("\n")[-3].split()[-1][1:-2]
        with open("{0}/trimmed_U.fastq".format(_tmp_dir), "w") as f:
            subprocess.call(["cat", "{0}/trimmed_1U.fastq".format(_tmp_dir), "{0}/trimmed_2U.fastq".format(_tmp_dir)], stdout=f)
        return float(drop_rate)
    return 0


def trim_files(input_files, tmp_dir, trimmomatic_jar, threads, skip_trim, skip_crop):
    """
    Trim input files.
    Trim is done on those not passing fastqc per seq quality. sliding window is used, and window increases each step until drop rate is less that 33%.
    For those passes fastqc, a minlen:100 trimming is done for format consistency.
    :param input_files: input fastq file
    :param tmp_dir: tmp directory
    :param trimmomatic_jar: trimmomatic jar file
    :return: None
    """
    if skip_trim:
        run_fake_trim(trimmomatic_jar, input_files, tmp_dir, threads)
        length = "250"
        with open(tmp_dir + "/trimmed_1P.fastq", "r") as f:
            f.readline()
            length = len(f.readline().strip())
        return str(length)

    window_steps = [4, 8, 12, 20, 35, 50, 70, 100]
    fastqc_dirs = ["", ""]

    # get fastqc for raw input
    for i, file in enumerate(input_files):
        if file.endswith(".fq.gz"):
            fastqc_dirs[i] = os.path.split(file)[-1].rstrip(".fq.gz") + "_fastqc"
        elif file.endswith(".fastq.gz"):
            fastqc_dirs[i] = os.path.split(file)[-1].rstrip(".fastq.gz") + "_fastqc"
        else:
            print("irregular file name provided, check your input")
            exit(1)
        run_fastqc(file, tmp_dir)
        # os.remove("%s/%s.html" % (tmp_dir, fastqc_dirs[i]))
        # os.remove("%s/%s.zip" % (tmp_dir, fastqc_dirs[i]))
    print("-" * 20 + "fastqc finished" + "-" * 20)

    with open("%s/%s/summary.txt" % (tmp_dir, fastqc_dirs[0]), "r") as f1, open("%s/%s/summary.txt" % (tmp_dir, fastqc_dirs[1]), "r") as f2:
        line1 = f1.readline()
        line1 = f1.readline()
        line2 = f2.readline()
        line2 = f2.readline()
        try:
            if line1.split()[0] == "PASS" and line2.split()[0] == "PASS":
                run_fake_trim(trimmomatic_jar, input_files, tmp_dir, threads)
                length = "250"
                with open(tmp_dir + "/trimmed_1P.fastq", "r") as f:
                    f.readline()
                    length = len(f.readline().strip())
                return str(length)
        except IndexError:
            sys.stderr.write("read summary indexerror at %s" % input_files[0] + "\n")
            return

    trim_condition = [window_steps[0], 20, *check_crop(tmp_dir, fastqc_dirs)]
    while trim_condition is not False:
        subprocess.call(["rm", "-rf", "{0}/trimmed_*.fastq".format(tmp_dir)])
        drop_rate = run_trim(trimmomatic_jar, input_files, tmp_dir, threads, skip_crop, *trim_condition)
        print("-" * 10, drop_rate)
        if drop_rate > 33 and trim_condition != window_steps[-1]:
            trim_condition[0] = window_steps[window_steps.index(trim_condition[0]) + 1]
        else:
            trim_condition = False
        print("-" * 20 + "trim finished" + "-" * 20)
    length = "250"
    print("%s/%s/fastqc_data.txt" % (tmp_dir, fastqc_dirs[0]))
    with open("%s/%s/fastqc_data.txt" % (tmp_dir, fastqc_dirs[0]), "r") as f:
        for line in f:
            if line.startswith("Sequence length"):
                print(line)
                length = line.strip().split()[-1]
                break
    return length


def main():
    supported_assemblers = ["spades", "skesa", "abyss", "masurca"]
    description = """
    This is the genome assembly pipeline of team1 group1.
    For detail please see github(https://github.gatech.edu/compgenomics2019/Team1-GenomeAssembly)
    """

    usage = """
    assemble_pipeline_g1.py -t /path/to/tmp -i /path/to/file1 /path/to/file2 -o /path/to/out/file -a spades -a skesa -a abyss
    """

    parser = argparse.ArgumentParser(description=description, usage=usage)
    parser.add_argument('--trimmomatic', default="Trimmomatic-0.36/trimmomatic-0.36.jar", metavar="trimmomatic_jar_file", help='provide trimmomatic file if you want to use your own')
    parser.add_argument('-a', required=True, choices=supported_assemblers, action="append", help='assemblers to use')
    parser.add_argument('-i', required=True, metavar=("file1", "file2"), nargs=2, help='pair end input files. MUST be gzipped fastq file')
    parser.add_argument('-t', default="tmp", metavar="tmp folder", help='tmp folder, if exist, will be cleared')
    parser.add_argument('-o', required=True, metavar="out_file", help='output file name')
    parser.add_argument('-n', type=int, default=1, metavar="num_threads", help='number of threads to use in trimming. default: 1')
    parser.add_argument('-k', action="store_true", help='set this flag to keep tmp. default:False')
    parser.add_argument('--skip-crop', action="store_true", help='set to true to skip crop step')
    parser.add_argument('--trim-only', action="store_true", help='set to true to skip assembly')
    parser.add_argument('--assemble-only', action="store_true", help='set to true to skip trim')
    args = parser.parse_args()

    if os.path.exists(args.t):
        shutil.rmtree(args.t)
    os.mkdir(args.t)
    print("-" * 20 + "%s cleared. Now start." % args.t + "-" * 20)
    fastqc_dir = trim_files(args.i, args.t, args.trimmomatic, args.n, args.assemble_only, args.skip_crop)
    print(os.listdir(args.t))
    if not args.trim_only:
        assemble_genomes(args.t, args.a, args.n, args.o, fastqc_dir)

    if not args.k:
        shutil.rmtree(args.t)
    print("-" * 20 + "all finished" + "-" * 20)
    # print("assembled genome is in %s" % args.o)


if __name__ == "__main__":
    main()