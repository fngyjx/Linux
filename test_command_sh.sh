#!command -v sh

echo "My Shell is [
`ps -p $$`
]"

#My Shell is [
#  PID TTY          TIME CMD
#15696 pts/2    00:00:00 bash
#]

#-bash: ./test_command_sh.sh: command: bad interpreter: No such file or directory
#If run in subshell
