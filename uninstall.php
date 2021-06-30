<?php

$addon = rex_addon::get('pwa');
rex_dir::delete($addon->getDataPath());

unlink('../manifest.json');
unlink('../service-worker.js');

