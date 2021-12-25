<?php

function enqueue_modules( $eggenv, $doc, $env ) {
	$k = null;
	$k = new consumeregg; //module1egg;
	$k->tgc = new tgc_generic(dirname($doc,1),$env);
	$k->q = new echo_out;
	$eggenv->enqueue_idents( $k, [ 'tgc', 'hello' ] );
}

?>
