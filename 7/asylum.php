<?php
#    asylum.php - Egg-SGML
#    Copyright 2020, 2021 Brian Jonnes

#    Egg-SGML is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, version 3 of the License.

#    Egg-SGML is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.

#    You should have received a copy of the GNU General Public License
#    along with Egg-SGML.  If not, see <https://www.gnu.org/licenses/>.

class tgctestc_dictionary {
	public $tsq, $tsr, $tsw;
	public $writernode;
	
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
		$env->write( sr_amp_lt( $env->diag_diplomats() ) );
	}
};

class tgctestc_pagename {
	public $tsq, $tsr, $tsw, $tsidents;
	
	function __construct( $a ) {
		$this->a = $a;
	}
	function tap( $env, $tsr ) {
		$env->write( sr_amp_lt( $this->a ) );
	}
};
class tgctestc_cachecontrol {
	public $tsq, $tsr, $tsw, $tsidents;
	
	function __construct( $a ) {
		$this->a = $a;
	}
	function tap( $env, $tsr ) {
		$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
		$d = eggsgml_descendent( $d, 'cache_control' );
		if( $d ) {
			if( attribute_exists( $d, 'static' ) ) {
				$env->write('static');
			} else if( attribute_exists( $d, 'dynamic' ) ) {
				$env->write('dynamic');
			} else if( attribute_exists( $d, 'querystring' ) ) {
				$env->write('querystring');
			}
		}
	}
};

class tgc_test_c__cell {
	public $tsq, $tsr, $tsw, $tsidents;
	public $dn;

	public $path, $env;
	public $n, $a, $sanity;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->n = opendir( $_SERVER['DOCUMENT_ROOT'] );
		$this->sanity = 0;
	}
	function read() {
		while(1) {
			$this->a = readdir($this->n);
			if( $this->a === false ) {
				return false; }
			if( $this->a[0] != '.' ) break;
			//if( strtoupper( substr($this->a,strlen($this->a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
			//	break; }
		}
		return true;
	}
	function tap( $env, $tsr ) {
		if( $tsr ) {
			if( ! $this->read() ) { $this->sanity = 1; return; }
			if( $this->b == $this->patients ) return;
			$this->b += 1;
		} else {
			$this->b = 1;
		}
		$env->enqueue_idents( $this, [ '*cache_control_value', '*page_name', '*dictionary' ]  );
		enqueue_subtree( $env, $this->dn );
	}
	function this_and_that( $r, $env, $w ) {
		$d = null;
		switch($w->nodeName) {
		case 'dictionary':
			$r->egg = new tgctestc_dictionary;
			return 2;
		case 'cache_control_value':
			$r->egg = new tgctestc_cachecontrol($this->a);
			return 2;
		case 'page_name':
			$r->egg = new tgctestc_pagename($this->a);
			return 2;
		}
		return 0;
	}
};

class tgctestc_ward {
	public $tsq, $tsr, $tsw;
	public $dn;
	public $m;
	function __construct($path,$env,$clipname) {
		$this->m = new tgc_test_c__cell($path,$env,$clipname);
	}
	function tap( $env, $tsr ) {
		if( $tsr ) {
			if( $this->m->sanity ) return;
		}
		$env->enqueue_idents( $this, [ '*cell' ] );
		enqueue_subtree( $env, $this->dn );
	}
	function this_and_that( $r, $env, $w ) {
		switch( $w->nodeName ) {
		case 'cell':
			$this->m->dn = $w;
			$this->m->patients = $w->getAttribute('patients');
			$r->egg = $this->m;
			return 1; //3;
		}
		return 0;
	}
};

class tgctestc__asylum {
	public $tsq, $tsr, $tsw;

	public $dn;

	public $path, $env;
	public $m;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->m = new tgctestc_ward($this->path,$this->env,$clipname);
	}
	function open() {
		return $this->m->read();
	}
	function tap( $env, $tsr ) {
		if( $tsr ) return;
		if( ! $this->m->m->read() ) {
			$this->NF = newframe( new tgc_test_c__empty, $q, $w );
			return;
		}
		$env->enqueue_idents( $this, [ '*ward', '*empty' ] );
		enqueue_subtree( $env, $this->dn );
	}
	function this_and_that( $r, $env, $w ) {
		switch( $w->nodeName ) {
		case 'empty':
			return 0;
		case 'ward':
			$this->m->dn = $w;
			$r->egg = $this->m;
			return 1; //3;
		}
		return 0;
	}
};

class tgctestc {
	public $tsq, $tsr, $tsw;
	public $writernode;

	public $tgcnode;
	public $tgc, $dn;

	public $q;

	public $path;
	function initialize($path,$w) {
		$this->path = $path;
		$this->dn = $w;
	}
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
		if( $tsr ) return;
		$env->enqueue_idents( $this, [ '*asylum' ] );
		enqueue_subtree( $env, $this->dn );
	}
	function this_and_that( $r, $env, $w ) {
		$c = null;
		switch( $w->nodeName ) {
		case 'asylum':
			$c = new tgctestc__asylum($this->path,$env,'clipname');
			$c->dn = $w;
			$r->egg = $c;
			return 1;
		}
		return 0;
	}
};

return new tgctestc;
?>
