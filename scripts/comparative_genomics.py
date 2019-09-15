#!/usr/bin/env python3
import argparse
import os
import subprocess
import sys
import pandas as pd
import itertools
import createDiffMatrix

def kSNP3(inFile, outDir):
    ## Automatically create in file, makefasta, kchooser for user and run kSNP3
    input_path = os.path.abspath(inFile)
    file_head = input_path.split('/')[::-1]
    # Copy .fasta contigs file (from Genome Assembly output) into directory w/ database contigs for MakeKSNPinfile command
    copyFasta = "cp /projects/VirtualHost/predicta/web_src/storage/app/uploads/assemble/{}_genome.fasta /projects/VirtualHost/predicta/web_src/storage/app/uploads/comparative/contigs/".format(file_head)
    subprocess.call(copyFasta, shell=True)
    # Creates input file, which is just a list of all of the genome file paths
    MakeKSNPin = "MakeKSNP3infile {} {}_infile A".format(input_path, file_head)
    subprocess.call(MakeKSNPin, shell=True)
    # Concatenates all genomic files for a fasta to optimize k-mer length
    makeFASTA = "MakeFasta {}_infile {}.fasta".format(file_head, file_head)
    subprocess.call(makeFASTA, shell=True)
    # Optimize k-mer length
    kCHOOSE_r = "Kchooser {}.fasta".format(file_head)
    subprocess.call(kCHOOSE_r, shell=True)
    # Parse Kchooser.report for optimal k-value
    dir_kc = os.getcwd()
    file_hand = open('dir_kc/Kchooser.report', 'r')
    k_val = 0
    for i in file_hand:
        if i.startswith('When'):
            k_val = int(i.split()[3])
    file_hand.close()
    # Run kSNP3 given input file and optimal k-mer length
    k_script = "kSNP3 -in {}_infile -outdir {} -k {} -ML | tee {}_log".format(file_head, outDir, k_val, file_head)
    subprocess.call(k_script, shell=True)
    removeFasta = "rm /projects/VirtualHost/predicta/web_src/storage/app/uploads/comparative/contigs/{}_genome.fasta".format(file_head)
    subprocess.call(removeFasta, shell=True)
    
def MASH(inFile):
    ## Compute MASH distance while querying to find potentially related strains
    input_path = os.path.abspath(inFile)
    for file in os.listdir(input_path):
        mash_cmd = "mash dist refseq.genomes.k21s1000.msh {} > distances_{}.tab".format(file, file.split('.')[0])
        subprocess.call(mash_cmd, shell=True)
        mash_out = "sort -gk3 distances_{}.tab | head -n1 >> strains_file.txt".format(file.split('.')[0])
        subprocess.call(mash_out, shell=True)

def calDifference1(inFile):
    """ before calculate difference for output of cgMLST, the last two columns need to be cut
    the output will be on the inFile """
    inFile_dropST=os.path.abspath(inFile) + "_dropST.csv" 
    dropColumns(inFile,inFile_dropST)
    outFile=os.path.join("/".join(os.path.abspath(inFile_dropST).split("/")[:-1]),"diffMatrix.csv")
    
    #after dropping ST columns, now can begin calculating the difference
    createDiffMatrix.main(inFile_dropST,outFile)

def dropColumns(inFile,outFile):
    file=pd.read_csv(inFile,sep='\t')
    file=file.drop(columns=['ST','clonal_complex']) ### drop columns
    file.to_csv(outFile,sep=',',index=False)     ### write into csv

def createMatrix(names,size):
    m=[[]]*(size+1)
    for i in range(size+1):
        m[i]=[[]]*(size+1)
    m[0][0]="name"
    for r in range(1,size+1):
        m[r][0]=names[r-1]
    for c in range(1,size+1):
        m[0][c]=names[c-1]
    #put 0= identical for m[i][j] where i==j, 100% means completely different
    for r in range(1,size+1):
        for c in range(1,size+1):
            if r==c:
                m[r][c]=0
    return m

def calDifference2(gene1,gene2):
    #iterate to each col and count different
    diff_list=[i!=j for i,j in zip(gene1,gene2)] #[True, False,True, False...]
    diff_val=diff_list.count(False)/len(gene1) #count difference
    return round(diff_val,4)


def create_diff_matrix(inputFile, outputFile):
    with open(inputFile, newline='') as csv_file:
        csv_reader = csv.reader(csv_file, delimiter=",")
        next(csv_reader, None)  # skip header
        row_list = list(csv_reader)  # all rows in cgMLST_matrix file, has 50 rows
        nameList = [l[0] for l in row_list]
        m = createMatrix(nameList, len(nameList))
        idx_list = [i for i in range(50)]
        pairs_list = list(itertools.combinations(idx_list, 2))

        for pair in pairs_list:
            gene1, gene2 = row_list[pair[0]][1:], row_list[pair[1]][1:]  # [1:] is to get rid of the first item CGT...
            gene1_idx, gene2_idx = pair[0] + 1, pair[1] + 1
            # print(gene1_idx,gene2_idx)
            diff = calDifference2(gene1, gene2)
            m[gene1_idx][gene2_idx] = diff
            m[gene2_idx][gene1_idx] = diff
        # print(DataFrame(m))
    with open(outputFile, "w") as outFile:  # use csv.writer
        wr = csv.writer(outFile)
        for r in m:
            wr.writerow(r)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("-m", "--mlst",action="store_true", help="running mentalist")
    parser.add_argument("-diff",action="store_true", help="calculate differences from cgMLST results")
    parser.add_argument("-i", "--inputFile",help="the path of input file")
    parser.add_argument("-o", "--outFile" ,help="path of output file/directory") 
    parser.add_argument("-db", "--database", help="path of database for cgMLST ") 
    parser.add_argument("-k", "--ksnp", action="store_true", help="running kSNP3")
    parser.add_argument("-mash", action="store_true", help="Run MASH distance on genomes")
    args = parser.parse_args()
    if args.mlst:
        if args.database == None:
            raise SystemExit("missing database to run mentalist. Exit")
        if args.inputFile==None:
            raise SystemExit("missing input sample file to run mentalist. Exit")
        if args.outFile==None:
            raise SystemExit("missing output name to run mentalist. Exit")
        else:
            cgMLST_call(args.inputFile,args.outFile,args.database)
    if args.diff:
        if args.inputFile==None:
            raise SystemExit("missing the result file of cgMLST calling to calculate the differences. Exit")
        calDifference(args.inputFile)
    if args.ksnp:
        if args.inFile==None:
            raise SystemExit("Please specify an input directory containing your genomes of analysis")
    if args.outFile==None:
        raise SystemExit("Please specify an output directory")
    kSNP3(args.inFile, args.outFile)
    if args.mash:
        if args.inFile==None:
            raise SystemExit("Please specify an input directory containing your genomes of analysis")
    MASH(args.inFile)


if __name__ == "__main__":
    main()