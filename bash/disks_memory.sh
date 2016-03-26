#!/bin/bash

FILE=/rcl/www/status/bash/disks_memory.txt

if [ $(find $FILE -cmin +1) ] ; then
#  cat /proc/meminfo | grep 'Mem' | sed -re 's|Mem(.+): +([0-9]+) kB|\1 \2|g' > $FILE
  free | grep ":" | sed -re 's|[^a-zA-Z]*([a-zA-Z]).+: +([0-9]+) +([0-9]+)( +([0-9]+))? ?.*|\1 \2 \3 \5|g' > $FILE
  touch $FILE
fi

cat $FILE
