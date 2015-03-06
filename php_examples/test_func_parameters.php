<?php

$x=1;
$y=2;

function my_func(&$a,$b) {

$a += 3;
$b += $a;

return $z= $a + $b;

}

echo "\$x=$x \n";
echo "\$y=$y \n";

$z=my_func($x,$y);

echo "\$x=$x \n";
echo "\$y=$y \n";
echo "\$z=$z \n";

function my_func_use_global() {

$GLOBALS['x'] += 10;
$GLOBALS['y'] += $GLOBALS['x'];
$GLOBALS['c'] = $GLOBALS['x'] + $GLOBALS['y'];

}

my_func_use_global();
echo var_dump($x);
echo "\n";
echo var_dump($y);
echo "\n";
echo var_dump($c);

echo var_dump($_SERVER);
