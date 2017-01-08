<?php
include('./lib/conf.php');
include('./lib/coder.php');

$list = [];
for($i = 0; $i < rand(0,count($reference) - 1); $i++){
	$list[] = $reference[$i];
}
$c = new Coder($reference,$passphrase,$iv);
echo $c->encrypt($c->pack($list));
?>
