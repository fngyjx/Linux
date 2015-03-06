#!/bin/sh

#1 temp var
IFS_save=$IFS
IFS=:
for dir in $PATH
do
echo $dir
done
IFS=$IFS_save

#2. Use external env coomand. Limitation: a. one command and b. external command

echo $PATH
env PATH=/opt/local/bin:$PATH ./printargs.sh this is my prog
echo $PATH

X=yes
export X
env X=no echo $X

#yes

env X=no sh -c 'echo $X'
#no
#3. As 2, but no env with the same limitation.
