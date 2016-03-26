#!/bin/bash

FILE=/rcl/www/status/bash/open_ports.txt

if [ $(find $FILE -cmin +1) ] ; then

  netstat -lntu | 
#    grep  ':\*' | grep '[tu][cd]p' | grep -o ':[0-9]* ' | grep -o '[0-9]*' | sort | uniq > $FILE
    grep  ':\*' | grep '[tu][cd]p' | sed -r 's/^(tcp|udp)6? .*(:[0-9]+).*$/\2 \1/' | sort | uniq > $FILE

  touch $FILE

fi

cat $FILE
