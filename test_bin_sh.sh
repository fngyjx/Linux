#!/bin/sh -x

echo "My Shell is [
`ps -p $$`
]"

echo "Number of arguments=["$#"]"
for i
do
echo "i=["$i"]"
done
#++ ps -p 5313
#+ echo 'My Shell is [
#  PID TTY          TIME CMD
# 5313 pts/2    00:00:00 test_bin_sh.sh
# ]'
# My Shell is [
# PID TTY          TIME CMD
#5313 pts/2    00:00:00 test_bin_sh.sh
#]

#sh -c "./test_bin_sh.sh test"
# and
#./test_bin_sh.sh test
#gave put same output
#++ ps -p 5607
#+ echo 'My Shell is [
#  PID TTY          TIME CMD
#   5607 pts/2    00:00:00 test_bin_sh.sh
#   ]'
#My Shell is [
#  PID TTY          TIME CMD
# 5607 pts/2    00:00:00 test_bin_sh.sh
#]
#+ echo 'Number of arguments=[1]'
#Number of arguments=[1]
#+ for i in '"$@"'
#+ echo 'i=[test]'
#i=[test]

