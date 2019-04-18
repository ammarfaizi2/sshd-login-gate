#!/usr/bin/env php
<?php

require __DIR__."/config.php";
require __DIR__."/src/autoload.php";

$slg = new \Slg\Daemon;
$slg->run();
