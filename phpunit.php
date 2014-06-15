<?php
include('include.php');

echo "Testing ... <br/><pre>";
echo shell_exec ("phpunit Tests/WorldTest.php");
echo "</pre>";