#!/usr/bin/env php
<?php

require __DIR__."/config.php";
require __DIR__."/src/autoload.php";

(new SshdLoginGate)->run();
