<?php   
    namespace LiteCheckr;
    require "form.php";

    $a = new Form("first_name", "last_name");


    $a->setValues([
        "first_name" => "Nels",
        "email" => "example@exxample.com"
    ]);

