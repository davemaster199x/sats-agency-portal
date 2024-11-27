<?php

$commands = [
    "git reset --hard HEAD",
    "git pull",
];


// add ?composer=1 to run composer as it wont be always needed
if($_GET['composer'] == 1){
    $commands[] = "/usr/local/bin/ea-php73 /opt/cpanel/composer/bin/composer install";
}

function getOutput($commands){
    $html = '';
    foreach($commands as $command){
        $result = array();
        $output = '';
        exec($command, $result);
        //print("<pre>");
        foreach ($result as $line) {
            //print($line . "\n");
            $output .= $line . "\r\n";
        }
        //print("</pre>");

        $html .= $command . "\r\n" . $output . "\r\n";
    }

    return $html;
}

echo getOutput($commands);