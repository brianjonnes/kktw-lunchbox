<?php

class mtgc_bcmlp {
	public $NF;
	public $path, $env;
	public $clipname;
	function initialize($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$c = null; $k = null; $a = '';
		switch($w->nodeName) {
		case 'testhttp':
			if( $end ) return 1;
			$a = 'secret=asdf&response=asdf'; // &remoteip=
			$k = stream_context_create( [ 'https' => [ 'method' => 'POST',
			   'header' => "Content-Type: application/x-www-form-url-encoded\r\nContent-Length: " . strlen($a),
			   'content' => $a ] ] );

			$c = fopen('https://www.google.com/recaptcha/api/siteverify','r',false,$k);
			$a = json_decode( stream_get_contents($c), true );
			$c = 0;
			//$q->write( sr_amp_lt( var_export($a) ) );
			$q->write( array_key_exists('success',$a) );
			return 2;
		case 'files':
			if( $end ) return 1;
	 		$c = new tgc_kktw_files($this->path,$this->env,attribute_exists($w,'folders'));
			if( $c->open() ) {
				$this->NF = newframe( $c, $q, $w );
			} else {
				$this->NF = newframe( new tgc_kktw_empty, $q, $w );
			}
			return 3;
		}
		return 0;
	}
}

return new mtgc_bcmlp;
?>
