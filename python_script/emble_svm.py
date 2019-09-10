# -*- coding: utf-8 -*-

def chunks(l, n):
    """Yield successive n-sized chunks from l."""
    for i in range(0, len(l), n):
        yield l[i:i+n]

def max(x, y):
    if x > y:
        return x
    return y 

def min(x, y):
    if x < y:
        return x
    return y

def direct2FormModel(data, a1, a2, b0, b1, b2):
    from numpy import zeros, arange
    
    result = zeros((len(data),))
    timeZone = zeros((len(data),))
    
    for n in arange(2, len(data)):
        sum0 = -a1*timeZone[n-1] - a2*timeZone[n-2]
        timeZone[n] = data[n] + sum0
        result[n] = b0*timeZone[n] + b1*timeZone[n-1] + b2*timeZone[n-2]
        
    return result
    
def differentialEqForm(data, a1, a2, b0, b1, b2):
    from numpy import zeros, arange
    
    result = zeros((len(data),))
    
    for n in arange(2, len(data)):
        result[n] = b0*data[n] + b1*data[n-1] + b2*data[n-2] - a1*result[n-1] - a2*result[n-2]
        
    return result
def filterSimpleHPF(data, tau, Ts):
    from numpy import zeros, arange
    
    result = zeros((len(data),))
    
    for n in arange(1, len(data)):
        result[n] = (tau*result[n-1] + tau*(data[n]-data[n-1]))/(tau + Ts)
        
    return result

def calcFFT(data, fs):
    from numpy.fft import fft
    import matplotlib.pyplot as plt

    n = len(data)
    k = np.arange(n)
    T = n/fs
    freq = k/T 
    freq = freq[range(int(n/2))]
    FFT_data = fft(data)/n 
    FFT_data = FFT_data[range(int(n/2))]

    return (FFT_data)

def draw_FFT_Graph(data, fs, **kwargs):
    from numpy.fft import fft
    import matplotlib.pyplot as plt
    
    graphStyle = kwargs.get('style', 0)
    xlim = kwargs.get('xlim', 0)
    ylim = kwargs.get('ylim', 0)
    title = kwargs.get('title', 'FFT result')
    
    n = len(data)
    k = np.arange(n)
    T = n/fs
    freq = k/T 
    freq = freq[range(int(n/2))]
    FFT_data = fft(data)/n 
    FFT_data = FFT_data[range(int(n/2))]
    
    plt.figure(figsize=(12,5))
    if graphStyle == 0:
        plt.plot(freq, abs(FFT_data), 'r', linestyle=' ', marker='^') 
    else:
        plt.plot(freq,abs(FFT_data),'r')
    plt.xlabel('Freq (Hz)')
    plt.ylabel('|Y(freq)|')
    plt.vlines(freq, [0], abs(FFT_data))
    plt.title(title)
    plt.grid(True)
    plt.xlim(xlim)
    plt.ylim(ylim)
    plt.show()

def SVMReg(X, y):
    # Generate sample data
    # import numpy as np

    # X = np.sort(5 * np.random.rand(40, 1), axis=0)
    # y = np.sin(X).ravel()

    ###############################################################################
    # Add noise to targets
    y[::5] += 3 * (0.5 - np.random.rand(8))

    ###############################################################################
    # Fit regression model
    from sklearn.svm import SVR

    svr_rbf = SVR(kernel='rbf', C=1e4, gamma=0.1)
    svr_lin = SVR(kernel='linear', C=1e4)
    svr_poly = SVR(kernel='poly', C=1e4, degree=2)
    y_rbf = svr_rbf.fit(X, y).predict(X)
    y_lin = svr_lin.fit(X, y).predict(X)
    y_poly = svr_poly.fit(X, y).predict(X)

    ###############################################################################
    # look at the results
    import pylab as pl
    pl.scatter(X, y, c='k', label='data')
    pl.hold('on')
    pl.plot(X, y_rbf, c='g', label='RBF model')
    pl.plot(X, y_lin, c='r', label='Linear model')
    pl.plot(X, y_poly, c='b', label='Polynomial model')
    pl.xlabel('data')
    pl.ylabel('target')
    pl.title('Support Vector Regression')
    pl.legend()
    pl.show()

import cv2
import numpy as np
import matplotlib.pyplot as plt
from scipy import signal
import math

cap = cv2.VideoCapture("data/data.mp4")

# X = np.sort(5 * np.random.rand(40, 1), axis=0)
# y = np.sin(X).ravel()
# SVMReg(X, y)

i=0

MAX = 0
x = []
y = []
rm_fft=[]
yM=0
MIN = 200
while True:
    
    (grabbed, frame) = cap.read()

    if not grabbed:
        break
    
    #frame resize
    (height, width) = frame.shape[:2]
    frame = frame[int(height*0.25):int(height*0.75),int(width*0.5-height*0.25):int(width*0.5+height*0.25)]
    (height, width) = frame.shape[:2]

    #rgb로 나누기
    (b, g, r) = cv2.split(frame)
    #평균
    rM = r.mean()
    gM = g.mean()
    bM = b.mean()
    
    if(MAX<=rM):
        MAX = rM
        
    if(MIN>=rM):
        MIN = rM
    x.append(i)
    y.append(rM)

    yM += rM
    
    i+=1
    
    cv2.imshow('video',frame)
    
    if cv2.waitKey(1) & 0xFF ==ord('q'):
        break

    
yM = yM / i
y = y - yM #평균으로 빼줌


Fs = MAX-MIN #진폭
Ts = 1.0/Fs 
t= np.arange(0,i,Ts)

n = len(y)
k = np.arange(n)
T = n/Fs
freq = k/T
freq = freq[range(int(n/2))]

Y = np.fft.fft(y)/n
Y = Y[range(int(n/2))]


fig, ax = plt.subplots(2, 1)
ax[0].plot(x,y)
ax[0].set_xlabel('Time')
ax[0].set_ylabel('Amplitude')
ax[0].grid(True)
ax[1].plot(freq, abs(Y), 'r', linestyle=' ', marker='^')




#indexes = scipy.signal.find_peaks_cwt(y,np.arange(0,len(y),MinDist))
# print(y)
plt.show()

# Design 1st Order High Pass Filter
f_cut = 0.5 #0.5Hz에서 cut-off
w_cut = 2*np.pi*f_cut
tau = 1/w_cut

num_z = np.array([tau/(tau+Ts), -tau/(tau+Ts)])
den_z = np.array([1., -tau/(tau+Ts)])

a1 = den_z[1]
a2 = 0
b0 = num_z[0]
b1 = num_z[1]
b2 = 0.

filteredSig1 = filterSimpleHPF(abs(Y), tau, Ts)
filteredSig2 = differentialEqForm(abs(Y), a1, a2, b0, b1, b2)
filteredSig3 = direct2FormModel(abs(Y), a1, a2, b0, b1, b2)

draw_FFT_Graph(filteredSig1, Fs, title='filterSimpleHPF', xlim=(0, 6))
draw_FFT_Graph(filteredSig2, Fs, title='differentialEqForm', xlim=(0, 6))
draw_FFT_Graph(filteredSig3, Fs, title='direct2FormModel', xlim=(0, 6))

fr = calcFFT(filteredSig3, Fs)
freqMax = 0
# j=0
# print(fr)
for i in fr:

    freqMax = max(freqMax, i)
    # print("aa")
    # print(freqMax)
    # print(filteredSig3[j])
    # print("aaaaa")

    # if(freqMax <= filteredSig3[j]):
    #     freqMax = filteredSig3[j]
    # j+=1
        
#Min Distance 계산
print(freqMax)
print('--------------')
print("<min distance>")
MinDist = (1/freqMax)*(3/4)
print(MinDist)
print('--------------')

#peak, trouugh 계산
window = (list(chunks(x, 20)))
prev = 0
now = 0
for w in window:
    max_value = 0
    min_value = 100000000
    prev = now

    for i in w:
        max_value = max(max_value, y[i])
        min_value = min(min_value, y[i])
        
        if max_value == y[i]:
            now = i


    print(now)
    print(prev)
    distance = now - prev 
    print(distance)
    print("-Mminmax")
    print(max_value+yM)
    print(min_value+yM)
    if min_value == 0:
        min_value = 0.00000001
    # print(max_value+ㅛㅡ/min_value)
    temp = (max_value+yM)/(min_value+yM)
    ir = math.log(temp)
    print("<ir>")
    print(ir)

    print("<feature selection>")
    peak = max_value
    trough = min_value
    Ipeak = peak
    Iac = peak - trough
    print(peak)
    print(trough)
    Ir = math.log(temp)

    print(Ir)



cap.release()
cv2.destroyAllWindows()