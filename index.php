<?php
include('./lib/conf.php');
include('./lib/coder.php');
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"></script>
	<script>
	require.config({
		paths: {
			'Coder': '/js/coder',
			'CryptoJS.aes': 'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes',
		},
		shim: {
			'CryptoJS.aes': {
				exports: 'CryptoJS'
			}
		}
	});
	</script>
	<script>require(['Coder'],function(Coder,CryptoJS){
		var source = <?= json_encode($reference); ?>,
			c = new Coder(source,'<?= $passphrase ?>','<?= $iv ?>');
		
		(function ask(){
			var slice = source.slice(Math.floor(Math.random() * source.length)),
				packed = c.pack(slice),
				encrypted = c.encrypt(packed),
				x = new XMLHttpRequest();
			
			x.open('POST','ajax.php',true);
			x.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			x.send('encrypted='+encodeURIComponent(encrypted));
			x.onreadystatechange = function(){
				if(x.readyState == 4){
					if(x.status == 200){
						console.log(slice.equal(JSON.parse(x.response)));
					}
				}
			};
			
			setTimeout(ask,7000);
		})();
		
		(function ask(){
			var x = new XMLHttpRequest();
			
			x.open('GET','ajax2.php',true);
			x.send(null);
			x.onreadystatechange = function(){
				if(x.readyState == 4){
					if(x.status == 200){
						var unpacked = c.decrypt(x.response),
							decrypted = c.unpack(unpacked);
						console.log(decrypted);
					}
				}
			};
			
			setTimeout(ask,1000);
		})();
	});</script>
</head>
<body>
<ul>
<li>index.php - демонстрационный скрипт.</li>
<li>ajax.php - получает закодированну строку, возвращает раскодированный массив.</li>
<li>ajax2.php - возвращает закодированну строку, для декодирования на клиенте.</li>
<li>lib/conf.php - исходные данные, справочник для формирования кода упаковщика.</li>
<li>lib/coder.php - серверная библиотека упаковщика.</li>
<li>js/coder.js - клиентская библиотека упаковщика и реализация задачи 3</li>
</body>
</html>
