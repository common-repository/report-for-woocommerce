<?php 
if( !class_exists( 'Woo_Function' ) ) {
	class Woo_Function{
		function __construct(){
		}
		function prettyPrint($a, $t='pre') {echo "<$t>".print_r($a,1)."</$t>";}
	}
}
?>