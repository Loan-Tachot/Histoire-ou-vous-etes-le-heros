<?php
class Page1_v {
    public function __destruct() {}
    public function __construct() {}
    public function rendumessage($message) 
    {
        $monrendu = file_get_contents("template\page1.html");
        $monrendu = str_replace('<!--$message -->',$message,$monrendu);
        return $monrendu;
    }
}
?>