<?php
class Coder{
	function __construct($source,$passphrase){
		$this->code = [];
		$this->sep = chr(48);
		$this->passphrase = $passphrase;
		
		$i = 49;
		
		foreach($source as $item){
			foreach(explode('/',$item) as $word){
				if(!isset($this->code[$word]))
					$this->code[$word] = chr($i++);
			}
		}
	}
	
	public function pack($data){
		$res = '';
		foreach($data as $str){
			foreach(explode('/',$str) as $word){
				$res .= $this->code[$word];
			}
			$res .= $this->sep;
		}
		return substr($res,0,-1);
	}
	
	public function unpack($str){
		$bc = array_flip($this->code);
		
		$res = [];
		foreach(explode($this->sep,$str) as $es){
			$tmp = '';
			
			foreach(str_split($es) as $c){
				$tmp .= $bc[$c].'/';
			}
			$res[] = substr($tmp,0,-1);
		}
		return $res;
	}
	
	public function encrypt($text){
		$crypted = '';
		$hk = $this->enlarge_your_key(strlen($text) - strlen($this->passphrase));
		
		for($i = 0; $i < strlen($text); $i++){
			$c = ord($text[$i]);
			// echo "encrypt: $i - $c";
			$crypted .= chr($c + ord($hk[$i]));
		}
		return base64_encode($crypted);
	}
	
	public function enlarge_your_key($diff){
		return $this->passphrase .
			str_repeat($this->passphrase,intval($diff / strlen($this->passphrase))) .
			substr($this->passphrase,0,$diff % strlen($this->passphrase));
	}
	
	public function decrypt($code){
		$code = base64_decode($code);
		$text = '';
		$hk = $this->enlarge_your_key(strlen($code) - strlen($this->passphrase));
		
		for($i = 0; $i < strlen($code); $i++){
			$c = ord($code[$i]) - ord($hk[$i]);
			$text .= chr($c);
		}
		
		return $text;
	}
	
	/*
	public function encrypt($value){
		$key = pack('H*',$this->passphrase);
		$iv = pack('H*',$this->iv);
		return base64_encode(openssl_encrypt($value,'aes-128-cbc',$key,true,$iv));
	}
	
	public function decrypt($str){
		$key = pack('H*',$this->passphrase);
		$iv = pack('H*',$this->iv);
		return openssl_decrypt(base64_decode($str),'aes-128-cbc',$key,true,$iv);
	}
	 */
}

$source = [
	'red/apple/candy/jujube/red','green/apple/candy','red/garnet/juice','green/grapes/candy',
	'orange/orange/coctail/alcohol','green/apple/jujube/green','green/grapes/ice cream','red/apple/candy',
	'orange/orange/juice','orange/orange/pie','red/strawberry/candy','red/strawberry/pie',
	'orange/orange/coctail/non alcohol','red/apple/puree','green/grapes/juice','green/grass/silo',
	'red/strawberry/juice','red/apple/candy/jujube/green','red/garnet/candy/red','red/grapes/ice cream',
	'red/garnet/candy/jujube','red/apple/candy/jujube/blue','green/apple/jujube/green','red/strawberry/ice cream',
	'red/apple/ice cream','red/garnet/pie','green/grass/hay','green/lime/coctail/non alcohol','red/grapes/juice',
	'green/lime/coctail/alcohol','green/lime/tea','green/apple/puree','green/lime/mousse','red/apple/candy/sweet',
	'green/apple/sweet','red/grapes/candy'];
$passphrase = '8c095ceb1f3b84e4943b4fd526c5461c';

$c = new Coder($source,$passphrase);
$packed = $c->pack($source);
$encrypted = $c->encrypt($packed);
$decrypted = $c->decrypt($encrypted);
$unpacked = $c->unpack($decrypted);

echo 'Packed:::: '.$packed."\n";
echo 'Decrypted: '.$decrypted."\n";
echo 'Encrypted: '.$encrypted."\n\n";
echo 'Unpacked:: '.json_encode($unpacked)."\n\n";
echo 'Source:::: '.json_encode($source)."\n\n";

?>
