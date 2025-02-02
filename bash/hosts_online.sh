#!/bin/bash

FILE=/rcl/www/status/bash/hosts_online.txt
FILE2=/rcl/www/status/bash/hosts_online2.txt

if ( [ "$1" == "force" ] || [ $(find $FILE -cmin +10) ] ) ; then
  # if (! empty($f1)) $f2 = $f1;
  if [ -s $FILE ] ; then
    cat $FILE > $FILE2
  fi

  {
  sudo -u root nmap -n -PO 192.168.4.1-30 192.168.4.33-38 -T5 --host-timeout 600ms \
    --min-hostgroup 64 --min-parallelism 64 --max-retries 2 --max-rtt-timeout 600ms ;
  } | grep -oP 'scan report for .*\(?\K192\.168\.\d{1,3}\.\d{1,3}(?=\)?)' > $FILE

  ip neigh show |
    grep 'lladdr' |
    grep -E '(REACHABLE|DELAY|PERMANENT)' |
    grep -E '^.*(192\.168\.[0-9]{1,3}\.[0-9]{1,3}|[0-9a-f\:]+\:[0-9a-f\:]) dev' |
    sed -re 's/^.*(192\.168\.[0-9]{1,3}\.[0-9]{1,3}|[0-9a-f\:]+\:[0-9a-f\:]) dev ([^ ]+[0-9]) lladdr ([0-9a-f]{2}(\:[0-9a-f]{2}){5}) .*/\1,\2,\3/' \
    >> $FILE

  sudo -k

  # if (empty($f1)) $f1 = $f2;
  if [ ! -s $FILE ] ; then
    cat $FILE2 > $FILE
  else
    touch $FILE
  fi
fi

cat $FILE
