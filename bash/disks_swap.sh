#!/bin/bash

FILE=/rcl/www/status/bash/disks_swap.txt

if ( [ "$1" == "force" ] || [ $(find $FILE -cmin +1) ] ) ; then
  cat /proc/meminfo | grep 'Swap[TF]' | sed -re 's|Swap(.+): +([0-9]+) kB|\1 \2|g' > $FILE
  touch $FILE
fi

cat $FILE
