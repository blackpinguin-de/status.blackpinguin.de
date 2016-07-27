#!/bin/bash

FILE=/rcl/www/status/bash/hosts_online.txt

if [ $(find $FILE -cmin +10) ] ; then

  {
  sudo -u root nmap -n -PO 192.168.4.1-15 192.168.4.18-20 -T5 --host-timeout 600ms --min-hostgroup 64 --min-parallelism 64 --max-retries 2 --max-rtt-timeout 600ms ;
  } | grep -oP 'scan report for .*\(?\K192\.168\.\d{1,3}\.\d{1,3}(?=\)?)' > $FILE

  ip neigh show | grep 'lladdr' | grep -E '(REACHABLE|DELAY|PERMANENT)' |
    sed -re 's/^.*(192\.168\.[0-9]{1,3}\.[0-9]{1,3}) dev ([^ ]+[0-9]) lladdr ([0-9a-f]{2}(\:[0-9a-f]{2}){5}) .*/\1,\2,\3/' >> $FILE

  sudo -k

  touch $FILE
fi

cat $FILE

