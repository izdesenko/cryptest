define(['CryptoJS.aes'],function(CryptoJS){
	var Coder = function(source,passphrase,iv){
		this.code = {};
		this.passphrase = CryptoJS.enc.Hex.parse(passphrase);
		this.iv = CryptoJS.enc.Hex.parse(iv);
		
		var i = 49,
			that = this;
		
		source
			.forEach(function(item){
				return item
					.split('/')
					.forEach(function(word){
						that.code[word] = that.code[word] || String.fromCharCode(i++);
					});
			});
	};
	
	Object.defineProperties(Coder.prototype,{
		/* Задача 1 */
		pack: {
			enumerable: false,
			value: function(list){
				var that = this;
				return list
					.map(function(str){
						return str
							.split('/')
							.map(function(word){return that.code[word];})
							.join('');
					})
					.join('0');
			}
		},
		unpack: {
			enumerable: false,
			value: function(str){
				var bc = this.code.flip();
				return str
					.split('0')
					.map(function(es){
						return es
							.split('')
							.map(function(c){return bc[c];})
							.join('/');
					});
			}
		},
		
		/* Задача 2 */
		encrypt: {
			enumerable: false,
			value: function(str){
				return CryptoJS.AES.encrypt(str,this.passphrase,{iv:this.iv}).ciphertext.toString(CryptoJS.enc.Base64);    
			}
		},
		decrypt: {
			enumerable: false,
			value: function(str){
				return CryptoJS.AES.decrypt(str,this.passphrase,{iv:this.iv}).toString(CryptoJS.enc.Utf8);
			}
		}
	});
	
	Object.defineProperties(Object.prototype,{
		flip: {
			enumerable: false,
			value: function(){
				var key,res={};
				for(key in this)
					if(this.hasOwnProperty(key))
						res[this[key]] = key;
				return res;
			}
		}
	});
	
	Object.defineProperties(Array.prototype,{
		equal: {
			enumerable: false,
			value: function(to){
				return this.length == to.length && this.every(function(element,index){
					return element === to[index]; 
				});
			}
		}
	});
	
		/* Задача 3 */
	// Полифилл для Function.bind
	// bind появляется в IE 9, до этой версии не было defineProperties, поэтому через прототип
	// if(!Function.prototype.bind){
	Function.prototype.bbind = function(self){
		var fn = this;
		return function(){
			return fn.apply(self,arguments);
		};
	};
	// }
	
	[
		null,undefined,NaN,'',0,10,[],
		[1,2,3,'test'],{},{a:1,b:2},new Map(),new Set(),
		function test(){console.log(test);}
	].forEach(function(ta){
		var f = function(){return this;},
			f1 = f.bind(ta),
			f2 = f.bbind(ta);
		
		console.log(deepCompare(f1(),f2()));
	});
	
	function deepCompare(){
		var i,l,leftChain,rightChain;
		
		function compare2Objects(x,y){
			var p;
			
			if(isNaN(x) && isNaN(y) && typeof x === 'number' && typeof y === 'number'){
				return true;
			}
			
			if(x === y){
				return true;
			}

			if(
				typeof x === 'function' && typeof y === 'function' 
				|| x instanceof Date && y instanceof Date
				|| x instanceof RegExp && y instanceof RegExp
				|| x instanceof String && y instanceof String
				|| x instanceof Number && y instanceof Number
			) return x.toString() === y.toString();

			
			if(!(x instanceof Object && y instanceof Object)) return false;
			if(x.isPrototypeOf(y) || y.isPrototypeOf(x)) return false;
			if(x.constructor !== y.constructor) return false;
			if(x.prototype !== y.prototype) return false;
			if(leftChain.indexOf(x) > -1 || rightChain.indexOf(y) > -1) return false;
			
			for(p in y){
				if(y.hasOwnProperty(p) !== x.hasOwnProperty(p)) return false;
				else if(typeof y[p] !== typeof x[p]) return false;
			}

			for(p in x){
				if(y.hasOwnProperty(p) !== x.hasOwnProperty(p)) return false;
				else if(typeof y[p] !== typeof x[p]) return false;
				
				switch(typeof x[p]){
				case 'object':
				case 'function':
					leftChain.push(x);
					rightChain.push(y);
					
					if(!compare2Objects(x[p],y[p])) return false;
					
					leftChain.pop();
					rightChain.pop();
					break;
				default:
					if(x[p] !== y[p]) return false;
					break;
				}
			}
			return true;
		}
		
		if(arguments.length < 1){
			return true;
		}
		
		for(i = 1,l = arguments.length; i < l; i++){
			leftChain = []; //Todo: this can be cached
			rightChain = [];
			
			if(!compare2Objects(arguments[0],arguments[i])) return false;
		}
		
		return true;
	}
	
	return Coder;
});
