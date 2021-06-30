<?php

$content = '';
$version = '';
$error  =  '';
$date = new DateTime();


$addon = rex_addon::get('pwa');

$func = rex_request('func', 'string');

if ($func == 'update') {


    $service_worker = fopen("../service-worker.js", "w") or die("Unable to open file!");
    $service_worker_content =
"self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open('sw-cache').then(function(cache) {
            return cache.add('index.html');
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

?>


<style>

    .fieldsetwrapper_pwa {
        border: 2px solid grey;
        background: #f1f1f1;
        padding: 20px;
        margin: 0 0 10px 0;
    }

    .fieldsetwrapper_pwa .help-block {
        font-size: 12px;
        margin-left: 1rem;
        display: inline-block;
    }

    .fieldsetwrapper_pwa .help-block.warning {
        color: #f00;
    }

    .fieldsetwrapper_pwa .fb_check {
        text-align: right;
        zoom: 0.75;
        margin: -90px 0 60px 0;
    }
    .fieldsetwrapper_pwa .colorpicker {
        width: 60px;
        height: 42px;
        margin-right: 12px;
        float: left;
    }
    .fieldsetwrapper_pwa .form-control.color {
        margin-top: 3px;
        width:  100px !important;
        float: left;
    }


    .fieldsetwrapper_pwa .fb_check label input[type=checkbox].toggle {
        margin-right: 8px;
        margin-top: -1px;
    }

    .fieldsetwrapper_pwa label input[type=checkbox].toggle {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        width: 3em;
        height: 1.5em;
        background: #ddd;
        vertical-align: middle;
        border-radius: 1.6em;
        position: relative;
        outline: 0;
        margin-right: 16px;
        cursor: pointer;
        -webkit-transition: background 0.1s ease-in-out;
        transition: background 0.1s ease-in-out;

    }
    .fieldsetwrapper_pwa label input[type=checkbox].toggle::after {
        content: '';
        width: 1.5em;
        height: 1.5em;
        background: white;
        position: absolute;
        border-radius: 1.2em;
        -webkit-transform: scale(0.7);
        transform: scale(0.7);
        left: 0;
        box-shadow: 0 1px rgba(0, 0, 0, 0.5);
        -webkit-transition: left 0.1s ease-in-out;
        transition: left 0.1s ease-in-out;
    }
    .fieldsetwrapper_pwa label input[type=checkbox].toggle:checked {
        background: #5791CE;

    }
    .fieldsetwrapper_pwa label input[type=checkbox].toggle:checked::after {
        left: 1.5em;
    }

    .fieldsetwrapper_pwa .modal {
        background: rgb(42, 57, 70, 80%);

    }

    .fieldsetwrapper_pwa .modal-content {
        width:100%;
        background: #efefef;
        border: 5px solid #555;
    }

    .fieldsetwrapper_pwa .modal-title {
        padding: 0 16px 0 16px;
    }

    .fieldsetwrapper_pwa .modal-body {
        padding: 32px;
        background: #fafafa;
    }

    .fieldsetwrapper_pwa.green {
        border: 1px solid green;
    }
    .fieldsetwrapper_pwa.orange {
        border: 1px solid orange;
    }
    .fieldsetwrapper_pwa.red,
    .fieldsetwrapper_pwa .error {
        border: 1px solid red;
    }

    .fieldsetwrapper_pwa.close {
        padding-top: 8px;
    }

    .fieldsetwrapper_pwa a { outline: none !important; }

    .fieldsetwrapper_pwa .modal-dialog-centered {
        display:-webkit-box;
        display:-ms-flexbox;
        display:flex;
        -webkit-box-align:center;
        -ms-flex-align:center;
        align-items:center;
        min-height:calc(100% - (.5rem * 2));
    }

    @media (min-width: 576px) {
        .fieldsetwrapper_pwa .modal-dialog-centered {
            min-height:calc(100% - (1.75rem * 2));
        }
    }
</style>
<script>
    $('#colorpicker_background').on('input', function() {
        $('#hexcolor-background').val(this.value);
    });
    $('#hexcolor-background').on('input', function() {
        $('#colorpicker_background').val(this.value);
    });

    $('#colorpicker_theme').on('input', function() {
        $('#hexcolor-theme').val(this.value);
    });
    $('#hexcolor-theme').on('input', function() {
        $('#colorpicker_theme').val(this.value);
    });

    $('#colorpicker_generated').on('input', function() {
        $('#hexcolor-generated').val(this.value);
    });
    $('#hexcolor-generated').on('input', function() {
        $('#colorpicker_generated').val(this.value);
    });
</script>