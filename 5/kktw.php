<?php

# kktw.php Copyright Brian Jonnes.

#We write an Egg-SGML module for the purpose of displaying files in a directory (or folder, if you can't help yourself).
# We keep a programming mirror (which doubles up as a coding mirror) next to us.
#
# To start off, we must choose a name for the module, and a prefix for the classes therein: Egg-SGML
# requires us to make sure all the modules play nicely together.
#
# This module will be the basis for all modules that this site (killkilltheworld.org.za) makes use of.
# And thus we find that our job is done for us :> thank you <:
#

class tgc_kktw_file {
	public $NF, $path, $env;
	public $n, $a, $isfolder;
	function __construct($path,$env,$isfolder) {
		$this->P = $_SERVER['DOCUMENT_ROOT'] . $env->urlpath;
		$this->path = $path;
		$this->env = $env;
		$this->n = opendir( $this->P );
		$this->isfolder = $isfolder;
	}
	function read() {
		if( $this->n === false ) return false;
		while(1) {
			$this->a = readdir($this->n);
			if( $this->a === false ) {
				return false; }
			if( $this->isfolder ) {
		// we note that the following test might fail
				if( is_dir( $this->P . '/' . $this->a ) ) {
					if( $this->a == '5' || $this->a == 'egg-sgml' ) {
					} else if( $this->a == '.well-known' ) {
					} else if( $this->a == '.' || $this->a == '..' || $this->a == '.git' ) {
					} else break;
				}
			} else if( is_dir( $this->P . '/' . $this->a ) ) {
		//wnt the flow control and the logic may not be the kinds that one finds O'Reilly recommending.
		//wnt while writing this we are reminded of odd-ball movies that were supposedly made by
		//wnt amateurs.
//			} else if( strtoupper( substr($this->a,strlen($this->a)-5) ) == '.XGML' ) {
			} else if( strtoupper( substr($this->a,strlen($this->a)-6) ) == '.CSSML' ) {
			} else if( strtoupper( substr($this->a,strlen($this->a)-6) ) == '.SHTML' ) {
			} else if( strtoupper( substr($this->a,strlen($this->a)-3) ) == '.ML' ) {
			} else break;
		// we comment out lines which we removed from the original, as a reminder
		// that we must copy fixes back to the original, if we discover any errors here.
		// the original for this object-class is egg-sgml/5/tgc_test_c.php
		//	if( strtoupper( substr($this->a,strlen($this->a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
		//		break; }
		}
		return true;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		if( $this->read() ) {
			return 1; }
		return 0;
	}
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$d = null;
		switch($w->nodeName) {
		case 'a':
			if( $end ) {
				$this->env->write_close_tag($q,'a');
				return 1; }
			$q->write('<a href="' . sr_amp_quot($this->env->urlpath . ( $this->env->urlpath == '/' ? '' : '/' ) . $this->a) . '"');
			write_attributes( $q, $w, [ 'href' ] );
			$this->env->write_end_of_tag($q,'a');
			return 2;
		case 'name':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->a ) );
			return 2;
		case 'clip_value':
			if( $end ) return 1;
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$this->NF = newframe($this->c, $q, $d);
			return 3;
		case 'image':
			if( $end ) return 1;
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$this->NF = newframe( new tgc_test_c__image($this->path,$this->env,$w), $q, $d );
			return 3;
		case 'cache_control_value':
			if( $end ) return 1;
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$d = eggsgml_descendent( $d, 'cache_control' );
			if( $d ) {
				if( attribute_exists( $d, 'static' ) ) {
					$q->write('static');
				} else if( attribute_exists( $d, 'dynamic' ) ) {
					$q->write('dynamic');
				} else if( attribute_exists( $d, 'querystring' ) ) {
					$q->write('querystring');
				}
			}
			return 2;
		case 'page_name':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->a ) );
			return 2;
		}
		return 0;
	}
};

class tgc_kktw_files {
	public $NF, $path, $env;
	public $m;
	function __construct($path,$env,$directories) {
		$this->path = $path;
		$this->env = $env;
		$this->m = new tgc_kktw_file($this->path,$this->env,$directories);
	}
	function open() {
		return $this->m->read();
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'empty':
			return 1;
		case 'item':
			if( $end ) return 1;
			$this->NF = newframe( $this->m, $q, $w );
			return 3;
		}
		return 0;
	}
};

class mtgc_kktw {
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
		$c = null;
		switch($w->nodeName) {
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

class tgc_kktw_empty {
	public $NF;
	public $inner, $w;
	function start( $q ) {
		$this->inner = false;
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		if( $this->inner ) {
			$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	}
	function consume( $q, $end, $w ) {
		if( $this->inner && $w === $this->w ) {
			// $end 
			$this->inner = false;
			return 1; }
		if( $w->nodeName == 'empty' ) {
			if( $this->inner )
				return 0;
			if( $end ) {
				// ! $w == $this->w
				return 1; }
			$this->inner = true;
			$this->w = $w;
			return 2;
		}
		if( $this->inner ) 
			return 0;
		return 2;
	}
};

return new mtgc_kktw;
?>
