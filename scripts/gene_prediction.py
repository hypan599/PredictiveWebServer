#!/usr/bin/env python3


import sys
import os 
import subprocess
import argparse

def genemark(i):
	
	if os.path.exists("./gms2results") != True:
		subprocess.call(["mkdir", "gms2results"])
	if os.path.exists("./gms2results/gfffiles") != True:
		subprocess.call(["mkdir","gms2results/gfffiles"])
	if os.path.exists("./gms2results/nucleotidefasta") != True:
		subprocess.call(["mkdir","gms2results/nucleotidefasta"])
	if os.path.exists("./gms2results/proteinfasta") != True:
		subprocess.call(["mkdir","gms2results/proteinfasta"])
	
	gff = os.path.join("gms2results/gfffiles","{}.gff".format(i.split(".")[0]))
	nucleotides = os.path.join("gms2results/nucleotidefasta","{}.fna".format(i.split(".")[0]))
	proteins = os.path.join("gms2results/proteinfasta","{}.faa".format(i.split(".")[0]))
	dir = i
	subprocess.call(["./gms2_linux_64/gms2.pl", "--seq", dir, "--genome-type", "bacteria", "--output",gff,"--format","gff","--fnn",nucleotides,"--faa",proteins])
	
def prodigal(i):
	if os.path.exists("./prodigalresults") != True:
		subprocess.call(["mkdir","prodigalresults"])
	if os.path.exists("./prodigalresults/nucleotide") != True:
		subprocess.call(["mkdir","prodigalresults/nucleotide"])
	if os.path.exists("./prodigalresults/protein") != True:
		subprocess.call(["mkdir","prodigalresults/protein"])
	if os.path.exists("./prodigalresults/gff") != True:
		subprocess.call(["mkdir","prodigalresults/gff"])
		
	protein = os.path.join("prodigalresults/protein","{}.faa".format(i.split(".")[0]))
	nucleotide = os.path.join("prodigalresults/nucleotide","{}.fna".format(i.split(".")[0]))
	gff = os.path.join("prodigalresults/gff","{}.gff".format(i.split(".")[0]))
	dir = i
	subprocess.call(["./Prodigal/prodigal","-i",dir,"-a",protein,"-d",nucleotide,"-o",gff,"-f","gff"])

def bedtools_func(i, home):
	if os.path.exists("./prodigal-genemark") != True:
		subprocess.call(['mkdir','prodigal-genemark'])
	if os.path.exists("./prodigal-genemark/gfffiles") != True:
		subprocess.call(['mkdir','prodigal-genemark/gfffiles'])
	if os.path.exists("./prodigal-genemark/gfffilesunion") != True:
		subprocess.call(['mkdir','prodigal-genemark/gfffilesunion'])
	if os.path.exists("./prodigal-genemark/nucleotides") != True:
		subprocess.call(['mkdir','prodigal-genemark/nucleotides'])
	if os.path.exists("./prodigal-genemark/aminoacids") != True:
		subprocess.call(['mkdir','prodigal-genemark/aminoacids'])

	prodigal_gff = os.path.join('prodigalresults','gff','{}.gff'.format(i.split(".")[0]))
	#gets gff files from prodigal
	genemark_gff = os.path.join('gms2results','gfffiles','{}.gff'.format(i.split(".")[0]))
	#gets gff files from genemark
	intersect1 = os.path.join('prodigal-genemark/gfffiles','{}intersect1.gff'.format(i.split(".")[0]))

	intersect2 = os.path.join('prodigal-genemark/gfffiles','{}intersect2.gff'.format(i.split(".")[0]))
	#gets intersect from genemark and prodigal
	common = os.path.join('prodigal-genemark/gfffiles','{}common.gff'.format(i.split(".")[0]))

	#gets common from genemark and prodigal
	union = os.path.join('prodigal-genemark/gfffilesunion','{}union.gff'.format(i.split(".")[0]))
	#gets union
	bedtools_intersect1 = ['bedtools2/bin/bedtools intersect -f 1.0 -r -wa -v -a {} -b {} > {}'.format(prodigal_gff,genemark_gff,intersect1)]

	bedtools_intersect2 = ['bedtools2/bin/bedtools intersect -f 1.0 -r -wa -v -b {} -a {} > {}'.format(prodigal_gff,genemark_gff,intersect2)]
	#command for intersect
	bedtools_common = ['bedtools2/bin/bedtools intersect -f 1.0  -r -a {} -b {} > {}'.format(prodigal_gff,genemark_gff,common)]
	#command for common

	subprocess.call(bedtools_intersect1,shell=True)
	subprocess.call(bedtools_intersect2,shell=True)
	subprocess.call(bedtools_common,shell=True)

	cat = ['cat {} {} {}> {}'.format(intersect1,intersect2,common,union)]
	#concatenates to get union
	subprocess.call(cat,shell=True)
	dir = i
	createfastaindex = ['samtools-1.9/bin/samtools','faidx',dir]
	#creates fasta index
	dnatoaapy = os.path.join(home,"nucltoprotein.py")
	subprocess.call(createfastaindex)
	nucleotides = os.path.join(home,"prodigal-genemark/nucleotides","{}.fna".format(i.split(".")[0]))
	fastasequences = ['bedtools2/bin/bedtools','getfasta','-fo',nucleotides,'-fi',dir,'-bed',union]
	amino = os.path.join(home,"prodigal-genemark/aminoacids","{}.faa".format(i.split(".")[0]))
	subprocess.call(fastasequences)
		
	#subprocess.call(['python3',dnatoaapy,nucleotides,amino])
	subprocess.call(['rm','-f','{}.fai'.format(dir)])

def barrnap(i):
	if os.path.exists("./barrnap_results") != True:
		subprocess.call(['mkdir','barrnap_results'])
	if os.path.exists("./barrnap_results/gfffiles") != True:
		subprocess.call(['mkdir','barrnap_results/gfffiles'])
	if os.path.exists("./barrnap_results/nucleotides") != True:
		subprocess.call(['mkdir','barrnap_results/nucleotides'])


	gff = os.path.join("barrnap_results/gfffiles","barnap_{}.gff".format(i.split(".")[0]))
	nucleotides = os.path.join("barrnap_results/nucleotides","barrnap_{}.fna".format(i.split(".")[0]))
	dir = i
	subprocess.call(['barrnap/bin/barrnap --outseq {} < {} > {}'.format(nucleotides,dir,gff)],shell=True)

def aragorn(i):
	if os.path.exists("./aragorn_results") != True:
		subprocess.call(['mkdir','aragorn_results'])
	if os.path.exists("./aragorn_results/gfffiles") != True:
		subprocess.call(['mkdir','aragorn_results/gfffiles'])
	if os.path.exists("./aragorn_results/nucleotides") != True:
		subprocess.call(['mkdir','aragorn_results/nucleotides'])

	gff = os.path.join("aragorn_results/gfffiles","aragorn_{}.gff".format(i.split(".")[0]))
	nucleotides = os.path.join("aragorn_results/nucleotides","aragorn_{}.fna".format(i.split(".")[0]))
	tRNAtxt = os.path.join("aragorn_results","{}.txt".format(i.split(".")[0]))
	dir = i

	subprocess.call(["aragorn1.2.38/aragorn","-t","-m","-gc1","-w",dir,"-fo","-o",nucleotides])
	subprocess.call(["aragorn1.2.38/aragorn","-t","-m","-gc1","-w",dir,"-o",tRNAtxt])
	subprocess.call(["/usr/bin/perl","cnv_aragorn2gff.pl","-i",dir,"-o",tRNAgff, "-gff-ver=2"])

def join(a,b):
	subprocess.call(['mkdir','arabarr'])

	for i,j in zip(sorted(os.listdir(a)),sorted(os.listdir(b))):
		subprocess.call('cat aragorn_results/nucleotides/{} barrnap_results/nucleotides/{} > arabarr/arabarr_{}.fna'.format(i,j,i.split("_")[1]),shell=True)
	
def main():
	# Initialize argument parser for script flags
	parser = argparse.ArgumentParser(description='Predict genes from assembled prokaryotic genomes.')
	# Sets arguments, requirements to run script, and type of argument input
	# help='sets description to be used by default ./script.py -h'
	parser.add_argument('-f', help='File for assembled genome input.', required=True, type=str)
	parser.add_argument('-p', help='Run Prodigal for ab-initio protein coding gene predictor.', required=False, action='store_true')
	parser.add_argument('-g', help='Run GeneMarkS-2 for ab-initio protein coding gene predictor.', required=False, action='store_true')
	parser.add_argument('-nc', help='Runs Bar Aragorn andrnap for non-coding RNA prediction.', required=False, action='store_true')
	parser.add_argument('-ncs', help='Runs Aragorn and Barrnap independently.', required=False, action='store_true')
	
	args = parser.parse_args()
	# Error handling for file input path
		
	# Variable for current working directory
	home = os.getcwd()
	# Options to run either prodigal or genemark
	if args.p:
		prodigal(args.f)
	if args.g:
		genemark(args.f)
	# Runs bedtools if both genemark and prodigal are selected
	if (args.p and args.g):
		bedtools_func(args.f, home)
	# Default mode to run both prodigal and genemark with bedtools_func
	if not args.p:
		if not args.g:
			prodigal(args.f)
			genemark(args.f)
			bedtools_func(args.f, home)
	
	# Runs aragorn and barrnap if selected
	if args.nc:
		aragorn(args.f)
		barrnap(args.f)
		join('aragorn_results/nucleotides','barrnap_results/nucleotides')
	if args.ncs:
		aragorn(args.f)
		barrnap(args.f)
	
if __name__ == '__main__':
	main()