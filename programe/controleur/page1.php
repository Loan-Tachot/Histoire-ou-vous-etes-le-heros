<?php
include ("modele\Page1-model.php");
include ("vue\page1.php");

class Page1 {
    public function afficher_message() 
    {
        $m = new page1_m();
        $v = new page1_v();
        echo $v->rendumessage($m->getmessage());
    }
    public function __construct() 
    {

    }
    public function __destruct() 
    {

    }



}


?>