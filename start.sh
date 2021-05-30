#!/bin/sh
#============ get the file name ===========
php awesome timer >/dev/null &
pids[0]=$!

while [ 1 ]; do
    for item in ${pids[*]}
    do
        isRun=$(ps aux | awk '{print $2}'| grep -w $item)
        echo ${isRun}
        if [ ! $isRun ];then
          exit
        fi
    done
    sleep 5
done
