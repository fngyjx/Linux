#!/bin/sh
select name in this is a select
do 
if [ $name == select ]
then 
	echo $name
	break
fi
echo "you choose \$REPLY="$REPLY
done

select name
do
if [[ $name == select ]]
then
	break
fi
echo "you choose \$REPLY="$REPLY
echo "the value is "$name
done
