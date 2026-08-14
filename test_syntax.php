<?php
function checkDir($d) {
    $files = glob($d . '/*.php');
    foreach($files as $f) {
        exec('php -l ' . escapeshellarg($f), $out, $ret);
        if($ret !== 0) echo $f . "\n";
    }
    $dirs = glob($d . '/*', GLOB_ONLYDIR);
    foreach($dirs as $dir) checkDir($dir);
}
checkDir('c:\\Users\\raulm\\Downloads\\nexorapos\\app\\Services');
