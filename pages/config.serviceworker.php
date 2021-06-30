<?php

$content = '';
$version = '';
$error  =  '';
$date = new DateTime();


$addon = rex_addon::get('pwa');

$func = rex_request('func', 'string');

if ($func == 'update') {

    $this->setConfig(rex_post('config', [
        ['pages_to_cache', 'array[int]']
    ]));



    $service_worker = fopen("../service-worker.js", "w") or die("Unable to open file!");
    $service_worker_content =
"self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open('sw-cache').then(function(cache) {
            return cache.add('/');
        })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        })
    );
});";

    fwrite($service_worker, $service_worker_content);
    fclose($service_worker);

    echo rex_view::success('Die <b>service-worker.js</b> wurde ge- bzw. überschrieben.');
    $this->setConfig('service-worker.js', true);
    rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
        $suchmuster= '<a href="index.php?page=pwa/config/serviceworker">service-worker.js <i style="color: #f00;" class="rex-icon fa-exclamation-triangle"></i></a>';
        $ersetzen = '<a href="index.php?page=pwa/config/serviceworker">service-worker.js</a>';
        $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
    });

}




// --------
// -------- Cache Pages
// --------


$content .= '<div class="fieldsetwrapper_pwa green">';
$content .= '<fieldset>';
$content .= '<legend>Cache';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#cache">(Mehr Informationen)</a>';
$content .= '</legend>';

$formElements = [];
$n = [];
$n['label'] = '<label for="pages_to_cache">Seiten, die gechached werden sollen</label>';
$category_select = new rex_category_select(false, false, true, true);
$category_select->setName('config[pages_to_cache][]');
$category_select->setId('pages_to_cache');
$category_select->setSize('10');
$category_select->setMultiple(true);
$category_select->setAttribute('class', 'selectpicker show-menu-arrow form-control');
$category_select->setAttribute('data-actions-box', 'true');
$category_select->setAttribute('data-live-search', 'true');
$category_select->setAttribute('data-size', '20');
$category_select->setSelected($this->getConfig('pages_to_cache'));
$n['field'] = $category_select->get();
$formElements[] = $n;
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


$content .= '</fieldset>';

$content .= ' 
<div class="modal fade" id="cache" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Cache
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body">
            <p>
                Egestas id vulputate magna in tempus porttitor ligula sit, senectus parturient himenaeos ultricies per ut sagittis varius, arcu aptent elit vestibulum potenti adipiscing nisi.
            </p>
       </div>      
    </div>
  </div>
</div>
';

$content .= '</div>';




$formElements = [];
$n = [];
$n['field'] = '<a class="btn btn-abort" href="' . rex_url::currentBackendPage() . '">Abbrechen</a>';
$formElements[] = $n;

$n = [];
$n['field'] = '<button class="btn btn-apply rex-form-aligned" type="submit" name="send" value="1"' . rex::getAccesskey(rex_i18n::msg('update'), 'apply') . '>Einstellungen speichern</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title','Einstellungen');
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content = '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <input type="hidden" name="func" value="update">
        ' . $content . '
    </form>';

echo $content;