#!/usr/bin/python

from datetime import timedelta

with open('/proc/uptime', 'r') as f:
    ts = int(round(float(f.readline().split()[0])))
    tm = ts / 60
    th = tm / 60

    s = str(ts % 60)
    m = str(tm % 60)
    h = str(th % 24)
    d = str(th / 24)

    if len(s) == 1:
        s = "0"+s
    if len(m) == 1:
        m = "0"+m
    if len(h) == 1:
        h = "0"+h

print(d + "d " + h + "h " + m + "m")
