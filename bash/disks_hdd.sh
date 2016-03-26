#!/bin/bash

FILE=/rcl/www/status/bash/disks_hdd.txt

if [ $(find $FILE -cmin +1) ] ; then

  df | grep '^/dev/' | sed -re 's|.* ([0-9]+) +([0-9]+) +[0-9]+ +[0-9]+% +(/.*)|\1 \2 \3|g' > $FILE
  touch $FILE

fi

cat $FILE
