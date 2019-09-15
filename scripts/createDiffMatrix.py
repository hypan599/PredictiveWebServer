import csv
import numpy as np
import itertools
import pandas as pd
import sys
import subprocess
import os

if __name__=="__main__":
    _, path, outfile = sys.argv
    files = os.listdir(path)
    mat = np.zeros((50, 50))
    for i, j in itertools.combinations(files, 2):
        idi = files.index(i)
        idj = files.index(j)
        f1 = os.path.join(path, i)
        f2 = os.path.join(path, j)
        mash_cmd = subprocess.Popen(["../../team1tools/ComparativeGenomics/mash-Linux64-v2.0/mash", "dist", f1, f2], stdout=subprocess.PIPE)
        mash_out, _ = mash_cmd.communicate()
        mash_out= mash_out.decode("utf-8").split()
        print(mash_out)
        mat[idi][idj] = float(mash_out[2])
        mat[idj][idi] = float(mash_out[2])
    df = pd.DataFrame(mat)
    print(df.shape)
    df.to_csv("50_distances.csv", header=False, index=False)
