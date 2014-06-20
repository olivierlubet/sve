<?php
include('include.php');

echo "Testing ... <br/><pre>";
echo shell_exec ("phpunit tests");
echo "</pre>";