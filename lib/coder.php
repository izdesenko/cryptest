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
?>
