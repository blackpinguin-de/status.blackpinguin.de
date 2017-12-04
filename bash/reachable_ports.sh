#!/bin/bash

FILE=/rcl/www/status/bash/reachable_ports.txt
FILE2=/rcl/www/status/bash/reachable_ports2.txt

if ( [ "$1" == "force" ] || [ $(find $FILE -cmin +10) ] ) ; then
  # if (! empty($f1)) $f2 = $f1;
  if [ -s $FILE ] ; then
    cat $FILE > $FILE2
  fi

  ip=$(/rcl/scripts/ipv4/current_public.sh)
  tcp="22,25,80,443,465,587,995,5222,5269,60001,60002"
  udp="8080"
  options="--host-timeout 600ms --min-hostgroup 64 --min-parallelism 64 --max-retries 2 --max-rtt-timeout 600ms"
  sudo nmap -PE $ip -p$tcp -T3 $options | grep -oP '^[0-9]+/tcp +[a-z\|]+' > $FILE
  sudo nmap -sU $ip -p$udp -T5 $options | grep -oP '^[0-9]+/udp +[a-z\|]+' >> $FILE

  # if (empty($f1)) $f1 = $f2;
  if [ ! -s $FILE ] ; then
    cat $FILE2 > $FILE
  else
    touch $FILE
  fi
fi

cat $FILE
