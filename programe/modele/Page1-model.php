<?php
class page1_m 
{
    private string $message;
    public function __construct()
    {
        $this->message = "Pouic!";
    }
    public function __destruct()
    {}
    public function getmessage()
    {
        return $this->message;
    }
}
?>