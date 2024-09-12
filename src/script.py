# script.py
import sys

def complex_calculation(x, y):
    return x * y

if __name__ == "__main__":
    x = int(sys.argv[1])
    y = int(sys.argv[2])
    result = complex_calculation(x, y)
    print(result)
