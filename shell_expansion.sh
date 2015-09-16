#!/bin/sh
# Expansion is performed on the 
#	command line
# after it has been split into
# 	words.
# There are seven kinds of expansion performed: 
# 1. brace  expansion, *
# 2. tilde  expansion,
# 3. parameter and variable expansion,
# 4. arithmetic expansion,
# 5. command substitution,
# 6. word splitting,  *
# 7. pathname expansion. *
# 
# The order of expansions is as above. where 3,4,and 5 have same priveledges and
# done in a left-to-right fashion.
# 
# On systems that can support it, there is an additional expansion avail-
# able: process substitution.
# 
# Only brace expansion, word splitting, and pathname expansion can change
# the number of words of the expansion; other expansions expand a single
# word  to a single word.  The only exceptions to this are the expansions
# of "$@" and "${name[@]}".
# 

#Brace Expansion
#       Brace expansion is a mechanism by which arbitrary strings may be gener-
#       ated. This  mechanism is similar to pathname expansion, but the file-
#       names generated need not exist
#	For  example,
#    a{d,c,b}e expands into ‘ade ace abe’.
# where a is called optional preamble and e is the optional postscript.
# syntax: 
#	preamble{comma-separate-string||sequence-expresion}postscript.
# where sequence-expresion formed as {x..y}. x and y maybe integers or charactors
# the expansion is inclusive. x and y must be the same type.
echo "a{d,c,b}e=[" a{d,c,b}e "]" # preamble{comma-separate-string||sequence-expresion}postscript
#a{d,c,b}e=[ ade ace abe ]
echo "a{d,c,b}{x..z}e=[" a{d,c,b}{x..z}e "]"
#a{d,c,b}{x..z}e=[ adxe adye adze acxe acye acze abxe abye abze ]
#usage example:
#              mkdir /usr/local/src/bash/{old,new,dist,bugs}
#       or
#              chown root /usr/{ucb/{ex,edit},lib/{ex?.?*,how_ex}}
echo /src/bash/{old,new,dist,bugs}
#/src/bash/old /src/bash/new /src/bash/dist /src/bash/bugs

echo /usr/{ucb/{ex,edit},lib/{ex?.?*,how_ex}}
#/usr/ucb/ex /usr/ucb/edit /usr/lib/ex?.?* /usr/lib/how_ex
echo /usr/{ucb/{100..102},lib/{a..c}}
#   Tilde Expansion
#       If  a  word 
# begins 
#	 with an unquoted tilde character (‘~’), all of the
#       characters preceding the first unquoted slash (or  all  characters,  if
#       there  is no unquoted slash) are considered a tilde-prefix.
echo "#   Tilde Expansion"
echo "\~\/ shell HOME or user's HOME " ~/
echo "\~zhong\/ login name \"zhong\"" ~zhong/
echo "\~nobody\/" ~nobody/
echo "\~+\/ shell PWD" ~+/
echo "\~-\/ shell OLDPWD" ~-/
pushd /usr/local 
pushd $HOME
pushd $HOME/Desktop
pushd $HOME/xlt_fo
dirs +2
echo "\~2\/ 2nd element of the directory stack" ~2/ #pushd command adding up dirs stack
