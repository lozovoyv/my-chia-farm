#!/bin/bash
DELAY=$2
while read line
do
  sleep $DELAY
  echo $line
done < $1
