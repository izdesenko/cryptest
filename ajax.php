<?php
include('./lib/conf.php');
include('./lib/coder.php');

$c = new Coder($reference,$passphrase,$iv);
echo json_encode($c->unpack($c->decrypt($_POST['encrypted'])));
?>
