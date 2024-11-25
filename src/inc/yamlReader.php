<?php

// require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";
require $_SERVER['DOCUMENT_ROOT'] ."/src/vendor/autoload.php";
use Symfony\Component\Yaml\Yaml;

function read_yaml($file_path) {


    if (!file_exists($file_path)) {
        throw new Exception("File not found: $file_path");
    }

    $yaml_content = file_get_contents($file_path);

    if ($yaml_content === false) {
        throw new Exception("Error reading file: $file_path");
    }

    $data = Yaml::parse($yaml_content);

    if ($data === false && $yaml_content !== '') {
        throw new Exception("Error parsing YAML in file: $file_path");
    }

    return $data ?? [];
}

?>
