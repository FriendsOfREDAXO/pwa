<?php

$addon = rex_addon::get('pwa');

rex_view::addCssFile($addon->getAssetsUrl('css/style.css'));
rex_view::addJsFile($addon->getAssetsUrl('js/script.js'), [rex_view::JS_IMMUTABLE => true]);


if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('pwa[]');
    rex_perm::register('pwa[config]');
}

if (rex::isBackend() && rex::getUser()) {

    if($addon->getConfig('manifest.json') == false){
        rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
            $suchmuster = '<a href="index.php?page=pwa/config/manifest">manifest.json</a>';
            $ersetzen = '  <a href="index.php?page=pwa/config/manifest">manifest.json <i style="color: #f00;" class="rex-icon fa-exclamation-triangle"></i></a>';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    }
    if($addon->getConfig('service-worker.js') == false){
        rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
            $suchmuster = '<a href="index.php?page=pwa/config/serviceworker">service-worker.js</a>';
            $ersetzen = '<a href="index.php?page=pwa/config/serviceworker">service-worker.js <i style="color: #f00;" class="rex-icon fa-exclamation-triangle"></i></a>';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    }

}

if (rex::isFrontend()) {
    if($addon->getConfig('manifest_include_frontend') == 1) {
        rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
            $suchmuster = '</head>';
            $ersetzen = '   <link rel="manifest" href="/manifest.json">
                         </head>';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    }
    if($addon->getConfig('serviceworker_include_frontend') == 1) {
        rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
            $suchmuster = '</body>';
            $ersetzen = '   
           <script>
            if ("serviceWorker" in navigator) {
                window.addEventListener("load", function() {
                    navigator.serviceWorker.register( "/service-worker.js").then(
                        function(erfolg) {
                        console.log( "Die ServiceWorker wurde registriert.", erfolg);
                    }
                    ).catch(
                        function(fehler) {
                            console.log( "Die ServiceWorker wurde leider nicht registriert.", fehler);
                        }
                    );
                });
            }
            </script>
        </body>';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    }
}


