<?php
#    tgc_test_c.php - Egg-SGML
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

class tgc_test_c__empty {
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

class tgc_test_c__image_b {
	public $NF, $path, $env;
	function __construct($path,$env,$v) {
		$this->path = $path;
		$this->env = $env;
		$this->v = $v;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'src':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->v->getAttribute('src') ) );
			return 2;
		case 'alt':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->v->getAttribute('alt') ) );
			return 2;
		}
		if( $end ) {
			$q->write('</' . $w->nodeName . '>');
			return 1; }
		$q->write('<' . $w->nodeName );
		write_attributes( $q, $w, [ ] );
		$q->write('>');
		return 2;
	}
};

class tgc_test_c__image_c {
	public $NF, $path, $env;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) { }
	function consume( $q, $end, $w ) {
		return 0; }
};

class tgc_test_c__image {
	public $NF, $path, $env;
	function __construct($path,$env,$v) {
		$this->path = $path;
		$this->env = $env;
		$this->v = $v;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) { }
	function consume( $q, $end, $w ) {
		$m = null;
		switch( $w->nodeName ) {
		case 'img':
			if( $end ) return 1;
			$this->NF = newframe( new tgc_test_c__image_b($this->path,$this->env,$w), $q, $this->v );
			return 3;
		case 'include':
			if ($end) return 1;
			$m = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $w->getAttribute('path') );
			$this->NF = newframe(new tgc_test_c__image_c($this->path,$this->env),$q,$m);
			return 3;
		}
		if( $end ) return 1;
		return 2;
	}
};

class tgc_test_c__a {
	public $NF, $path, $env;
	public $clipname;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->clipname = $clipname;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) { }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'record':
			if( $end ) return 1;
			if( $w->getAttribute('id') == $this->clipname ) {
				$this->NF = newframe( new tgc_sgml_source(''), $q, $w );
				return 3; }
			return 1;
		}
		if( $end ) return 1;
		return 2;
	}
};

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
	public $tsq, $tsr, $tsw;
	public $writernode;
	
	function __construct( $a ) {
		$this->a = $a;
	}
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
		$env->write( sr_amp_lt( $this->a ) );
	}
};
class tgctestc_cachecontrol {
	public $tsq, $tsr, $tsw;
	public $writernode;
	
	function __construct( $a ) {
		$this->a = $a;
	}
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
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
	public $tsq, $tsr, $tsw;
	public $writernode;

	public $tgcnode;
	public $tgc, $dn;

	public $q;


	public $NF, $path, $env;
	public $n, $a, $sanity;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->n = opendir( $_SERVER['DOCUMENT_ROOT'] );
		$this->c = new tgc_test_c__a($path,$env,$clipname);
		$this->sanity = 0;
	}
	function read() {
		while(1) {
			$this->a = readdir($this->n);
			if( $this->a === false ) {
				return false; }
			break;
			if( strtoupper( substr($this->a,strlen($this->a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
				break; }
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
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
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
		enqueue_subtree( $env, $this->dn, $this->tgcnode, $this->writernode );
	}
	function this_and_that( $r, $env, $w ) {
		$d = null;
		switch($w->nodeName) {
		case 'dictionary':
			$r->egg = new tgctestc_dictionary;
			return 2;
		case 'clip_value':
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$this->NF = newframe($this->c, $q, $d);
			return 3;
		case 'image':
			if( $end ) return 1;
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$this->NF = newframe( new tgc_test_c__image($this->path,$this->env,$w), $q, $d );
			return 3;
		case 'cache_control_value':
			$r->egg = new tgctestc_cachecontrol($this->a);
			return 2;
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
			return 2;
		case 'page_name':
			$r->egg = new tgctestc_pagename($this->a);
			return 2;
			$env->write( sr_amp_lt( $this->a ) );
			return 2;
		}
		return 0;
	}
};

class tgctestc_ward {
	public $tsq, $tsr, $tsw;
	public $writernode;

	public $tgcnode;
	public $tgc, $dn;

	public $q;

	public $m;
	function __construct($path,$env,$clipname) {
		$this->m = new tgc_test_c__cell($path,$env,$clipname);
	}
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
		if( $tsr ) {
			if( $this->m->sanity ) return;
		}
		$env->enqueue_idents( $this, [ '*cell' ] );
		enqueue_subtree( $env, $this->dn, $this->tgcnode, $this->writernode );
	}
	function this_and_that( $r, $env, $w ) {
		switch( $w->nodeName ) {
		case 'cell':
			$this->m->dn = $w;
			$this->m->writernode = $r->writernode;
			$this->m->tgcnode = $r->tgcnode;
			$this->m->patients = $w->getAttribute('patients');
			$r->egg = $this->m;
			return 1; //3;
		}
		return 0;
	}
};

class tgc_test_c__pages {
	public $tsq, $tsr, $tsw;
	public $writernode;

	public $tgcnode;
	public $tgc, $dn;

	public $q;

	public $NF, $path, $env;
	public $m;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->m = new tgctestc_ward($this->path,$this->env,$clipname);
	}
	function open() {
		return $this->m->read();
//		$this->n = opendir( $_SERVER['DOCUMENT_ROOT'] );
//		while(1) {
//			$this->a = readdir($this->n);
//			if( $this->a === false ) {
//				return false; }
//			if( strtoupper( substr($this->a,strlen($this->a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
//				return true; }
//		}
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
		if( $tsr ) return;
		if( ! $this->m->m->read() ) {
			$this->NF = newframe( new tgc_test_c__empty, $q, $w );
			return;
		}
		$env->enqueue_idents( $this, [ '*ward', '*empty' ] );
		enqueue_subtree( $env, $this->dn, $this->tgcnode, $this->writernode );
	}
	function this_and_that( $r, $env, $w ) {
		switch( $w->nodeName ) {
		case 'empty':
			return 0;
		case 'ward':
			$this->NF = $this->m; //newframe( $this->m, $q, $w );
			$this->m->dn = $w;
			$this->m->writernode = $r->writernode;
			$this->m->tgcnode = $r->tgcnode;
			$r->egg = $this->m;
			return 1; //3;
		}
		return 0;
	}
};

class mtgc_test_c {
	public $NF;
	public $path, $env;
	public $clipname;
	function initialize($path,$env) {
		$this->path = $path;
		$this->env = $env;
		if( array_key_exists('clip',$_GET) ) {
			$this->clipname = $_GET['clip']; }
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
		case 'pages':
			if( $end ) return 1;
			$c = new tgc_test_c__pages($this->path,$this->env,$this->clipname);
			//if( $c->open() ) {
				$this->NF = $c;
				$c->dn = $w;
				$this->NF->writernode = $q->stack->writernode;
				$this->NF->tgcnode = $q->stack->tgcnode;
				return 4;
				$this->NF = newframe( $c, $q, $w );
			//} else {
				$this->NF = newframe( new tgc_test_c__empty, $q, $w );
			//}
			return 3;
		}
		return 0;
	}
}

//class tgctestc_pages {
//	function this_and_that( $env, $type, $m ) {


class test_c_egg {
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
		enqueue_subtree( $env, $this->dn, $this->tgcnode, $this->writernode );
	}
	function this_and_that( $r, $env, $w ) {
		$c = null;
		switch( $w->nodeName ) {
		case 'asylum':
			$c = new tgc_test_c__pages($this->path,$env,'clipname');
			$c->dn = $w;
			$c->writernode = $r->writernode;
			$c->tgcnode = $r->tgcnode;
			$r->egg = $c;
			return 1;
		}
		return 0;
	}
};

return new test_c_egg;
?>
