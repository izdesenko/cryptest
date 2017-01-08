<?php
class Coder{
	function __construct($source,$passphrase,$iv){
		$this->code = [];
		$this->passphrase = $passphrase;
		$this->iv = $iv;
		
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
			$res .= '0';
		}
		return substr($res,0,-1);
	}
	
	public function unpack($str){
		$bc = array_flip($this->code);
		
		$res = [];
		foreach(explode('0',$str) as $es){
			$tmp = '';
			
			foreach(str_split($es) as $c){
				$tmp .= $bc[$c].'/';
			}
			$res[] = substr($tmp,0,-1);
		}
		return $res;
	}
	
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
}
?>
