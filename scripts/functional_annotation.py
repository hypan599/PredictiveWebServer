#!/usr/bin/env python3

import subprocess
import re
"""
FUNCTIONAL ANNOTATION PIPELINE FOR WEBSERVER
Author: Jacob Feldman

Import vfdb and CARD into main script
vfdb queries against nucleaic acid files and CARD against protein
~~All functions assume they are being run from predicta folder~~
"""
#------------------------------------------------------------------------------------------------

"""
Functions to blast against virulence factor DB
First function runs blast and second function converts output to .gff
Third function combines the two
"""
def vfdbBlast(inputFile):

    subprocess.call(["team1tools/FunctionalAnnotation/ncbi-blast-2.9.0+/bin/blastn", # todo: fix path
                    "-db", "team1tools/FunctionalAnnotation/vfDB", 
                    "-query", inputFile, 
                    "-num_threads", "4", 
                    "-evalue" ,"1e-10", 
                    "-outfmt", "'6 stitle qseqid pident qcovs qstart qend qseq evalue bitscore'",
                    "-best_hit_score_edge", "0.1", 
                    "-best_hit_overhang", "0.1",
                    "-max_target_seqs", "1", 
                    "-out", "vfdb_temp"])

def vfdb_to_gff(inputFile, outputFile):

    output_name = outputFile + ".gff"
    output = open(output_name, "w+")

    with open(inputFile,"r",encoding='latin-1') as fh:
         for l in fh:
             l=l.strip("\n").split("\t")
             notes=l[0]
             seqid=l[1]
             start=l[5]
             end=l[8]
             output.write("{}\tVFDB-BLAST\tBacterial Virulent genes\t{}\t{}\t.\t.\t.\t{}\n".format(seqid,start,end,notes))
    output.close()  

def vfdb(inputFile, outputFile):

    vfdbBlast(inputFile)

    vfdb_to_gff("vfdb_temp", outputFile)

    subprocess.call(["rm", "vfdb_temp"])


#-------------------------------------------------------------------------------------------------
"""
Functions to query against CARD antibiotic resistance DB
First function runs rgi and second function converts output to .gff
Third function combines the two
"""

def CARD_rgi(inputFile):

    card = "/team1tools/FunctionalAnnotation/rgi-4.2.2/card.json"
    model = "/team1tools/FunctionalAnnotation/rgi-4.2.2/protein_fasta_protein_homolog_model.fasta" # todo: fix path

    subprocess.run(["rgi", "load",
        "-i", card,
        "--card_annotation", model,
        "--local"])

     subprocess.run(["rgi", "main",
        "-i", inputFile,
        "-o", "card_temp",
        "--input_type", "protein",
        "--local"])

def rgi_to_gff(inputFile, outputFile):

    file = open(inputFile, 'r', encoding='latin-1')
    next(file)

    output_name = outputFile + ".gff"
    output = open(output_name, "w")
    output.write("##gff-version 3\n")

    for line in file:
         line=re.sub('\s+', '\t', line).strip().split("\t")
         #print(line)
         seqid=line[0]
         start=line[2]
         end=line[4] 
         notes=line[12:-5]
         notes=';'.join(notes)
         output.write("{}\tRGI-CARD\tAntibiotic resistant genes\t{}\t{}\t.\t.\t.\t{}\n".format(seqid,start,end,notes))

    file.close()
    output.close()

def CARD(inputFile, outputFile):

    cardtemp = "card_temp.txt"
    cardtemp2 = "card_temp.json"
    
    CARD_rgi(inputFile)

    rgi_to_gff(cardtemp, outputFile)

    subprocess.call(["rm", cardtemp])
    subprocess.call(["rm", cardtemp2])

#------------------------------------------------------------------------------------------------
if __name__ == "__main__":
    pass
    