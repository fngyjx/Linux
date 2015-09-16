# #!/bin/sh
echo "\$\_=[" $_ "] without quote"
echo "\$\_=[ $_ ] in side quote"
# 
# Special Parameters
# The shell treats several parameters specially.   These  parameters  may
# only be referenced; assignment to them is not allowed.
#  *      Expands  to  the positional parameters, starting from one.  When
#         the expansion occurs within double quotes, it expands to a  sin-
#         gle word with the value of each parameter separated by the first
#         character of the IFS special variable.  That is, "$*" is equiva-
#         lent to "$1c$2c...", where c is the first character of the value
#         of the IFS variable.  If IFS is unset, the parameters are  sepa-
#         rated  by  spaces.   If  IFS  is null, the parameters are joined
#         without intervening separators.
echo "\$\*=[" $* "] without quote"
echo "\$\*=[ $* ] in side quote"
a=("$*")
echo ${a[0]} #hello world
echo ${#a[0]} #12
echo ${#a[*]} #1
echo ${#a[@]} #1
#  @      Expands to the positional parameters, starting from  one.   When
#         the  expansion  occurs  within  double  quotes,  each  parameter
#         expands to a separate word.  That is, "$@" is equivalent to "$1"
#         "$2"  ...   If the double-quoted expansion occurs within a word,
#         the expansion of the first parameter is joined with  the  begin-
#         ning  part  of  the original word, and the expansion of the last
#         parameter is joined with the last part  of  the  original  word.
#         When  there  are no positional parameters, "$@" and $@ expand to
#         nothing (i.e., they are removed).
echo "\$\@=[" $@ "] without quote"
echo "\$\@=[ $@ ] in side quote"
a=("$@")
echo ${a[0]} # hello
echo ${#a[0]} # 5
echo ${#a[*]} # 2
echo ${#a[@]} # 2
#  #      Expands to the number of positional parameters in decimal.
echo "\$\#=[" $# "] without quote"
echo "\$\#=[ $# ] in side quote"
#  ?      Expands to the status of the most recently  executed  foreground
#         pipeline.
echo "\$\?=[" $? "]"
echo "\$\?=[ $? ]"

# -      Expands  to  the  current option flags as specified upon invoca-
# tion, by the set builtin command, or  those  set  by  the  shell
# itself (such as the -i option).
echo "\$\-=[" $- "] without quote"
echo "\$\-=[ $- ] in side quote"
# $      Expands  to  the  process ID of the shell.  In a () subshell, it
# expands to the process ID of the current  shell,  not  the  sub-
# shell.
# !      Expands  to  the  process ID of the most recently executed back-
# ground (asynchronous) command.
# 0      Expands to the name of the shell or shell script.  This  is  set
# at shell initialization.  If bash is invoked with a file of com-
# mands, $0 is set to the name of that file.  If bash  is  started
# with  the  -c option, then $0 is set to the first argument after
# the string to be executed, if one is present.  Otherwise, it  is
# set  to  the file name used to invoke bash, as given by argument
# zero.
# _      At shell startup, set to the absolute pathname  used  to  invoke
# the  shell or shell script being executed as passed in the envi-
# ronment or argument list.  Subsequently,  expands  to  the  last
# argument  to the previous command, after expansion.  Also set to
# the full pathname used  to  invoke  each  command  executed  and
# placed in the environment exported to that command.  When check-
# ing mail, this parameter holds the name of the  mail  file  cur-
# rently being checked.
# 
echo "\$\_=[" $_ "] without quote"
echo "\$\_=[ $_ ] in side quote"
