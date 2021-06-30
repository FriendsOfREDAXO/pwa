<?php

$content = '';
$version = '';
$error  =  '';
$date = new DateTime();


$addon = rex_addon::get('pwa');

$func = rex_request('func', 'string');

if ($func == 'update') {

    $this->setConfig(rex_post('config', [
        ['manifest_include_frontend', 'int'],
        ['serviceworker_include_frontend', 'int'],
        ['version', 'string'],
        ['image1024', 'string'],
        ['name', 'string'],
        ['short_name', 'string'],
        ['description', 'string'],
        ['lang', 'string'],
        ['start_url', 'string'],
        ['orientation', 'string'],
        ['background_color', 'string'],
        ['theme_color', 'string'],
        ['generated', 'string'],
        ['display', 'string']
    ]));

    echo rex_view::success('Die Einstellungen wurden gespeichert');

    if($this->getConfig('name') != '' AND $this->getConfig('short_name') != '' AND $this->getConfig('display') != '' AND $this->getConfig('image1024') != ''  AND $this->getConfig('lang') != '' AND $this->getConfig('orientation')) {

        if($this->getConfig('version') != '') {
            $version = $this->getConfig('version');
        } else {
            $version = $date->getTimestamp();

        }

        $manifest = fopen("../manifest.json", "w") or die("Unable to open file!");
        $manifest_content = '{'."\n";
        $manifest_content .= '"version" : "'.$version.'",'."\n";
        $manifest_content .= '"name" : "'.$this->getConfig('name').'",'."\n";
        $manifest_content .= '"short_name" : "'.$this->getConfig('short_name').'",'."\n";
        $manifest_content .= '"description" : "'.$this->getConfig('description').'",'."\n";
        $manifest_content .= '"lang" : "'.$this->getConfig('lang').'",'."\n";
        $manifest_content .= '"display" : "'.$this->getConfig('display').'",'."\n";
        $manifest_content .= '"orientation" : "'.$this->getConfig('orientation').'",'."\n";
        $manifest_content .= '"background_color" : "'.$this->getConfig('background_color').'",'."\n";
        $manifest_content .= '"theme_color" : "'.$this->getConfig('theme_color').'",'."\n";
        $manifest_content .= '"generated" : "'.$this->getConfig('generated').'",'."\n";

        if($this->getConfig('start_url') !='' ) {
            $manifest_content .= '"start_url" : "'.rex_geturl($this->getConfig('start_url')).'",'."\n";
        } else {
            $manifest_content .= '"start_url" : "/",'."\n";
        }

        $manifest_content .= '"scope" : ".",';
if($this->getConfig('image1024') !='' ) {


    $mmtypes = explode(',',$addon->getConfig('image_formats'));

    $manifest_content .= '
"icons" : [';

    foreach ($mmtypes as $size) {

        if ($size == '196') {
            $manifest_content .= '
    {
        "src": "' . rex_media_manager::getUrl('pwa' . $size, $this->getConfig('image1024')) . '",
        "sizes": "'.$size.'x'.$size.'",
        "type": "image/png",
        "purpose": "any maskable"
    }';
        } else {
            $manifest_content .= '
    {
        "src": "' . rex_media_manager::getUrl('pwa' . $size, $this->getConfig('image1024')) . '",
        "sizes": "'.$size.'x'.$size.'",
    }';
        }
        if(next($mmtypes)) {
            $manifest_content .= ',';
        }
    }
    $manifest_content .= '
  ],
  "splash_pages": null
}';
}

        $manifest_content .= '}'."\n";

        fwrite($manifest, $manifest_content);
        fclose($manifest);

        echo rex_view::success('Die <b>manifest.json</b> wurde ge- bzw. überschrieben.');
        $this->setConfig('manifest.json', true);
        rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
            $suchmuster = '  <a href="index.php?page=pwa/config/manifest">manifest.json <i style="color: #f00;" class="rex-icon fa-exclamation-triangle"></i></a>';
            $ersetzen = '<a href="index.php?page=pwa/config/manifest">manifest.json</a>';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });


    } else {
        echo rex_view::error('Die <b>manifest.json</b> wurde nicht erstellt. <b>Bitte prüfe alle Angaben!</b>');
    }

}




// --------
// -------- Allgemeines
// --------

if($this->getConfig('name') != '' AND $this->getConfig('short_name') != '') {
    $content .= '<div class="fieldsetwrapper_pwa green">';
} else {
    $content .= '<div class="fieldsetwrapper_pwa red">';
}


$content .= '<fieldset>';
$content .= '<legend>Allgemeines';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#name_modal">(Mehr Informationen)</a>';
$content .= '</legend>';


$content .= '<fieldset>';


$formElements = [];
$n = [];
$n['label'] = '<label for="version">Version</label>';
$n['field'] = '<input class="form-control " type="text" id="version" name="config[version]" placeholder="" value="' . $this->getConfig('version') . '"/>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');


$error = $this->getConfig('name') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="name" >Name<sup>*</sup></label>';
$n['field'] = '<input class="form-control '.$error.'" type="text" id="name" name="config[name]" placeholder="Bitte ausfüllen" value="' . $this->getConfig('name') . '"  required maxlength="45" />';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');


$formElements = [];
$n = [];
$n['label'] = '<label for="short_name" >Kurzer Name</label>';
$n['field'] = '<input class="form-control '.$error.'" type="text" id="short_name" name="config[short_name]" placeholder="Bitte ausfüllen" value="' . $this->getConfig('short_name') . '"  />';
$formElements[] = $n;


$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$formElements = [];
$n = [];
$n['label'] = '<label for="short_name">Beschreibung</label>';
$n['field'] = '<input class="form-control" type="text" id="description" name="config[description]" placeholder="Bitte ausfüllen" value="' . $this->getConfig('description') . '"/>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');


$error = $this->getConfig('lang') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="short_name">Sprache<sup>*</sup></label>';
$n['field'] = '<input class="form-control '.$error.'" type="text" id="lang" name="config[lang]" placeholder="Bitte ausfüllen (Bsp: de_DE)" value="' . $this->getConfig('lang') . '"/>';
$formElements[] = $n;



$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');


$content .= '</fieldset>';
$content .= ' 
<div class="modal fade" id="name_modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Allgemeines
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body">
            <p><b>Version</b><br/>
                Sofern keine Version angegeben wird wird hier bei jeden "speichern" ein Timestamp gesetzt.
            </p>
            <p><b>Name</b><br/>
                Der Name der PWA (maximal 45 Zeichen) ist der primäre Bezeichner und ist ein Pflichtfeld.
            </p>
            <p><b>Kurzname</b><br/>
                Der Kurzname (maximal 12 Zeichen empfohlen) ist eine Kurzversion des Namens der PWA. Es ist ein optionales Feld und wenn es nicht angegeben wird, wird der Name verwendet, obwohl er wahrscheinlich abgeschnitten wird. Der Kurzname wird normalerweise verwendet, wenn nicht genügend Platz für die Anzeige des vollständigen Namens vorhanden ist. 
            </p>
            <p><b>Beschreibung</b><br/>
                Hier kann die PWA beschrieben werden.
            </p>
       </div>      
    </div>
  </div>
</div>
';

$content .= '</div>';

// --------
// -------- Startseite
// --------


$content .= '<div class="fieldsetwrapper_pwa green">';
$content .= '<fieldset>';
$content .= '<legend>Startseite';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#startseite">(Mehr Informationen)</a>';
$content .= '</legend>';

$formElements = [];
$n = [];
$n['label'] = '<label for="start_url">Startseite</label>';
$category_select = new rex_category_select(false, false, true, true);
$category_select->setName('config[start_url]');
$category_select->setId('start_url');
$category_select->setSize('1');
$category_select->setAttribute('data-size', '1');
$category_select->setSelected($this->getConfig('start_url'));
$n['field'] = $category_select->get();
$formElements[] = $n;
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


$content .= '</fieldset>';

$content .= ' 
<div class="modal fade" id="startseite" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Startseite
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


// --------
// -------- Farben
// --------

$content .= '<div class="fieldsetwrapper_pwa green">';
$content .= '<fieldset>';
$content .= '<legend>Farben';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#farben">(Mehr Informationen)</a>';
$content .= '</legend>';


$error = $this->getConfig('background_color') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="background_color">Background Color<sup>*</sup></label>';
$n['field'] = '<input class="colorpicker '.$error.'" type="color" id="colorpicker_background" name="config[background_color]" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="'.$this->getConfig('background_color').'"><input class="form-control color '.$error.'" type="text" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="'.$this->getConfig('background_color').'" id="hexcolor-background"></input>';

$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


$error = $this->getConfig('theme_color') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="theme_color">Theme Color<sup>*</sup></label>';
$n['field'] = '<input class="colorpicker '.$error.'" type="color" id="colorpicker_theme" name="config[theme_color]" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="'.$this->getConfig('theme_color').'"><input class="form-control color '.$error.'" type="text" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="'.$this->getConfig('theme_color').'" id="hexcolor-theme"></input>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


$error = $this->getConfig('generated') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="generated">Generated<sup>*</sup></label>';
$n['field'] = '<input class="colorpicker '.$error.'" type="color" id="colorpicker_generated" name="config[generated]" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="'.$this->getConfig('generated').'"><input class="form-control color '.$error.'" type="text" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="'.$this->getConfig('generated').'" id="hexcolor-generated"></input>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


$content .= ' 
<div class="modal fade" id="farben" tabindex="-1" >
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Farben
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body"> 
            <p>Dictumst odio taciti nulla metus sagittis condimentum</p>
       </div>      
    </div>
  </div>
</div>
';

$content .= '</div>';



// --------
// -------- Bilder
// --------

$content .= '<div class="fieldsetwrapper_pwa green">';
$content .= '<fieldset>';
$content .= '<legend>Bilder';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#images">(Mehr Informationen)</a>';
$content .= '</legend>';


$error = $this->getConfig('image1024') == '' ? 'error' : '';

// Dateiauswahl Medienpool-Widget
$formElements = [];
$n = [];
$n['label'] = '<label for="REX_MEDIA_1">Bild<sup>*</sup> (PNG 1024px x 1024px)</label>';

$n['field'] = '
<div class="rex-js-widget rex-js-widget-media">
	<div class="input-group '.$error.'">
		<input class="form-control" type="text" name="config[image1024]" value="' . $this->getConfig('image1024') . '" id="REX_MEDIA_1" readonly="readonly">
		<span class="input-group-btn">
        <a href="#" class="btn btn-popup" onclick="openREXMedia(1);return false;" title="ÖFFNEN">
        	<i class="rex-icon rex-icon-open-mediapool"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="addREXMedia(1);return false;" title="NEU">
        	<i class="rex-icon rex-icon-add-media"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="deleteREXMedia(1);return false;" title="REMOVE">
        	<i class="rex-icon rex-icon-delete-media"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="viewREXMedia(1);return false;" title="ANSEHEN">
        	<i class="rex-icon rex-icon-view-media"></i>
        </a>
        </span>
	</div>
 </div>
';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');



$content .= ' 
<div class="modal fade" id="images" tabindex="-1" >
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Bilder
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body"> 
            <p>Alle notewendigen Bilder werden automatisch generiert.</p>
       </div>      
    </div>
  </div>
</div>
';

$content .= '</div>';



// --------
// -------- Orientation
// --------

if($this->getConfig('orientation') != '') {
    $content .= '<div class="fieldsetwrapper_pwa green">';
} else {
    $content .= '<div class="fieldsetwrapper_pwa red">';
}


$content .= '<fieldset>';
$content .= '<legend>Orientation';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#orientation">(Mehr Informationen)</a>';
$content .= '</legend>';

$error = $this->getConfig('orientation') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="orientation">Auswahl<sup>*</sup></label>';
$select = new rex_select();
$select->setId('orientation');
$select->setAttribute('class', 'form-control '.$error.'');
$select->setName('config[orientation]');
$select->addOption('Bitte wählen', '');
$select->addOption('Portrait', 'portrait');
$select->addOption('Landscape', 'landscape');
$select->setSelected($this->getConfig('orientation'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '</fieldset>';

$content .= ' 
<div class="modal fade" id="orientation" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Orientation
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body">
            <p><b>Portrait</b><br/>
                Egestas id vulputate magna in tempus porttitor ligula sit, senectus parturient himenaeos ultricies per ut sagittis varius, arcu aptent elit vestibulum potenti adipiscing nisi.
            </p>
            <p><b>Landscape</b><br/>
                Egestas id vulputate magna in tempus porttitor ligula sit, senectus parturient himenaeos ultricies per ut sagittis varius, arcu aptent elit vestibulum potenti adipiscing nisi.
            </p>
       </div>      
    </div>
  </div>
</div>
';

$content .= '</div>';



// --------
// -------- Display Mode
// --------

if($this->getConfig('display') != '') {
    $content .= '<div class="fieldsetwrapper_pwa green">';
} else {
    $content .= '<div class="fieldsetwrapper_pwa red">';
}


$content .= '<fieldset>';
$content .= '<legend>Display Mode';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#display_mode_modal">(Mehr Informationen)</a>';
$content .= '</legend>';

$error = $this->getConfig('display') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="display">Auswahl<sup>*</sup></label>';
$select = new rex_select();
$select->setId('display');
$select->setAttribute('class', 'form-control '.$error.'');
$select->setName('config[display]');
$select->addOption('Bitte wählen', '');
$select->addOption('Standalone', 'standalone');
$select->addOption('Fullscreen', 'fullscreen');
$select->addOption('Minimal-UI', 'minimal-ui');
$select->addOption('Browser', 'browser');
$select->setSelected($this->getConfig('display'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '</fieldset>';

$content .= ' 
<div class="modal fade" id="display_mode_modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Display Mode
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body">
            <p><b>Standalone</b><br/>
                Egestas id vulputate magna in tempus porttitor ligula sit, senectus parturient himenaeos ultricies per ut sagittis varius, arcu aptent elit vestibulum potenti adipiscing nisi.
            </p>
            <p><b>Fullscreen</b><br/>
                Egestas id vulputate magna in tempus porttitor ligula sit, senectus parturient himenaeos ultricies per ut sagittis varius, arcu aptent elit vestibulum potenti adipiscing nisi.
            </p>
            <p><b>Minimal-UI</b><br/>
                Egestas id vulputate magna in tempus porttitor ligula sit, senectus parturient himenaeos ultricies per ut sagittis varius, arcu aptent elit vestibulum potenti adipiscing nisi.
            </p>                
            <p><b>Browser</b><br/>
                Egestas id vulputate magna in tempus porttitor ligula sit, senectus parturient himenaeos ultricies per ut sagittis varius, arcu aptent elit vestibulum potenti adipiscing nisi.
            </p>            

            
       </div>      
    </div>
  </div>
</div>
';

$content .= '</div>';


// --------
// -------- EINBINDUNG
// --------

$content .= '<div class="fieldsetwrapper_pwa green">';
$content .= '<fieldset>';
$content .= '<legend>Einbindung';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#includes">(Mehr Informationen)</a>';
$content .= '</legend>';


$formElements = [];
$n = [];
$n['label'] = '<label><b>manifest.json</b> im Frontent laden</label>';
$n['field'] = '<input type="checkbox" class="toggle" id="manifest_include_frontend" name="config[manifest_include_frontend]"' . (!empty($this->getConfig('manifest_include_frontend')) && $this->getConfig('manifest_include_frontend') == '1' ? ' checked="checked"' : '') . ' value="1" />';
$formElements[] = $n;
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/checkbox.php');
$content .= '</fieldset>';


$formElements = [];
$n = [];
$n['label'] = '<label><b>service-worker.js</b> im Frontent laden</label>';
$n['field'] = '<input type="checkbox" class="toggle" id="serviceworker_include_frontend" name="config[serviceworker_include_frontend]"' . (!empty($this->getConfig('serviceworker_include_frontend')) && $this->getConfig('serviceworker_include_frontend') == '1' ? ' checked="checked"' : '') . ' value="1" />';
$formElements[] = $n;
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/checkbox.php');
$content .= '</fieldset>';

$content .= ' 
<div class="modal fade" id="includes" tabindex="-1" >
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Einbindung
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body"> 
            <p>Sollicitudin amet mollis ligula nibh viverra penatibus ultricies varius elementum nam aliquam congue inceptos, etiam quis urna elit dis ridiculus molestie consectetur orci lacus eros fames. Lectus ornare mollis dictumst gravida class habitasse elit dis, vel facilisis quis tincidunt augue dolor sit aenean consectetur, praesent vulputate feugiat ipsum facilisi felis etiam.</p>
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
