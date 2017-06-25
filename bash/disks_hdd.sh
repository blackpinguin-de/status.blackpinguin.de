#!/bin/bash

FILE=/rcl/www/status/bash/disks_hdd.txt
OLD_FILE=/rcl/www/status/bash/disks_hdd2.txt

if ( [ "$1" == "force" ] || [ $(find $FILE -cmin +1) ] ) ; then

  # what is currently mounted ?
  df | grep '^/dev/' | sed -re 's|.* ([0-9]+) +([0-9]+) +[0-9]+ +[0-9]+% +(/.*)|\1 \2 \3|g' > $FILE

  # devices that were mounted somewhen
  readarray unmounted < $OLD_FILE

  # remove mounted devices from array $unmounted
  for mp in $(grep -Eo "/.*$" $FILE) ;
  do
    tmp=$(echo "${unmounted[@]}" | grep -Ev " $mp\$" | grep -Eo "[0-9]+ [0-9]+ /.*\$" )
    unset unmounted
    unmounted=$tmp
    unset tmp
  done

  # save mounted devices in old file
  cat $FILE > $OLD_FILE

  # keep unmounted devices in old file
  echo "${unmounted[@]}" >> $OLD_FILE

  # save modification time
  touch $FILE
fi

cat $FILE
