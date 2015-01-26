#!/bin/sh

#The positinal parameter larger than 9 need to be curly braced such as ${10} the $10 will be substituted with $1 followed with 0.

echo "$0 has $# parameters"
echo
: ${12?The number of the parameters less than 15}
for i in {0..12}
do
case $i in
10) 
echo "No braces to position parameter ["$10"]"
echo "have braces to the position parameter ["${10}"]"
;;
11) 
echo "No braces to position parameter ["$11"]"
echo "have braces to the position parameter ["${11}"]"
;;
12) 
echo "No braces to position parameter ["$12"]"
echo "have braces to the position parameter ["${12}"]"
;;
*) echo "not the 10th and after param ["$i"]";;
esac
done
#Or use shift to get the positional values that beyound 9
echo
echo

for ar
do
echo $ar
done

echo 'parameter expansion to $*'
for ar in "$*"
do
echo $ar
done

echo 'parameter expansion to $@'
for ar in "$@"
do
echo $ar
done

#Above,  $* and $@ are same if not quoted, i.e. all space separated are parameters no matter in single or double quote. If the $* and $@ are quoted, got folowings on CentOS (5.22)

# ./substitute_fieldsplit.sh this is my shell command "in double quote" 'in single quote'
# this
# is
# my
# shell
# command
# in double quote
# in single quote
# parameter expansion to $*
# this is my shell command in double quote in single quote
# parameter expansion to $@
# this
# is
# my
# shell
# command
# in double quote
# in single quote

IFS=:

set -- $PATH
for dir
do
echo $dir
done
#Note: you cannot put the IFS assignment and the command on the same line - the command is parsed before the IFS assignment even though the command is run after the IFS assignment takes effect.
#IFS is not usually exported. The behavior of child shells to which IFS has been exported is not portable. Don't do that. 

#${CC:="-O2"}

echo "CC=[$CC]"
: ${CC:="-O2"} # /bin/true ${CC:="-O2"} works also but may come out with extra message of the "true". So use ':' not true here. 
echo "CC=[$CC]"
#Where ${CC:="-O2"} is the parameter of ':' command. It (:) does nothing but assign default value of CC if CC is undefined (NULL) or empty string. If omit ':' after CC then assignment will happen only when CC is undefined (null). Omit ':' command will cause shell error : -O2: command not found.
: ${1+$@} #here, should be no ':' after 1.

