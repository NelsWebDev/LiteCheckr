<?php

    namespace LiteCheckr;

    // require "form.php";
    // require "field.php";
    // require "rule.php";
    // require "helper.php";
    // require "rulehelper.php";
    
    spl_autoload_register(function($full_class_path){
        $path_without_base_ns = str_replace(__NAMESPACE__, "", $full_class_path);
        $path_without_base_ns = trim($path_without_base_ns, "\\");
        $file_path = strtolower(str_replace("\\", "/", $path_without_base_ns)) . ".php";
        require $file_path;
    });
