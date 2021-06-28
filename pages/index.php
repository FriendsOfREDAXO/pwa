<?php

$addon = rex_addon::get('pwa');
echo rex_view::title('Progressive Web App');
rex_be_controller::includeCurrentPageSubPath();
