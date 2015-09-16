#!/bin/sh
if [ -t 0 ]; then #stdin is opened and used  
  echo script running interactively
else
  echo stdin coming from a pipe or file
fi
if [ -t 1 ]; then #stdout is opened and used  
  echo output is going to the screen
else 
  echo output redirected to a file or pipe
fi

echo This is stdout
echo and this goes to stderr >&2
exec > output.txt
echo This is still stdout but goes elsewhere

echo but where does this go\? >&2
exec date
echo this script is kaput #This line would be ignored or never be reached because the "exec date" command terminated the current process and switched to "date" process.
#So if you want to end a shell scripts in the middle of its file, use "exec", Just like '__END__' in the perl scripts.
