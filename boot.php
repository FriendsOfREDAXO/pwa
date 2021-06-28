<?php

$addon = rex_addon::get('pwa');

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('pwa[]');
    rex_perm::register('pwa[config]');
}

if (rex::isBackend() && rex::getUser()) {

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


