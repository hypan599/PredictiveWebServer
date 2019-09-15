#!/usr/bin/env python

import sys
import re  

'''
FastQ Format:
@
GCTACT (Sequence)
+ (OR -)
Any character (! ' * () + % 5 C )
'''

def file_check():
    filename = sys.argv[1]
    #inFile = open(filename, 'r')
    #def fastq(filename):
    with open(filename, 'r') as inFile:
        line1 = inFile.readline()
        if line1.startswith('@'):
            line2 = inFile.readline()
            if re.match(r'^[ACGTNacgtn]+', line2):
                line3 = inFile.readline()
                if line3.startswith('+'):
                    line4 = inFile.readline()
                    if len(line4) == len(line2):
                        return True
                    else:
                        return False
                else:
                    return False
            else:
                return False
    return False

if __name__ == "__main__":
    print(("pass" if file_check() else "fail"), end="\n")