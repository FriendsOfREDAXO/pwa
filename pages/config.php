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
        ['image512', 'string'],
        ['name', 'string'],
        ['short_name', 'string'],
        ['description', 'string'],
        ['display_mode', 'string']
    ]));

    echo rex_view::success('Die Einstellungen wurden gespeichert');

    if($this->getConfig('name') != '' AND $this->getConfig('short_name') != '' AND $this->getConfig('display_mode') != '' AND $this->getConfig('image512') != '') {

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
        $manifest_content .= '"lang" : "de-DE",
        "start_url" : "/",
        "scope" : "/",
        "display" : "standalone",
        "orientation" : "portrait",
        "background_color" : "#fff",
        "theme_color" : "#000",
        "generated" : "fff",
        "icons" : [
        {
                "src": "https://kreischer.de/icon16.png",
                "sizes": "16x16"
        },
        {
            "src": "https://kreischer.de/icon192.png",
            "sizes": "192x192"
        },
	    {
	      "src": "https://kreischer.de/icon196.png",
	      "sizes": "196x196",
	      "type": "image/png",
	      "purpose": "any maskable"
	    },        
        {
            "src": "https://kreischer.de/icon256.png",
            "sizes": "256x256"
        },
        {
            "src": "https://kreischer.de/icon512.png",
            "sizes": "512x512"
        }
	]
    
    ';
    
    
    
        $manifest_content .= '}'."\n";

        fwrite($manifest, $manifest_content);
        fclose($manifest);

        echo rex_view::success('Die <b>manifest.json</b> wurde ge- bzw. überschrieben.');
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
$content .= '<legend>Name';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#name_modal">(Mehr Informationen)</a>';
$content .= '</legend>';


$content .= '<fieldset id="name">';


$formElements = [];
$n = [];
$n['label'] = '<label for="version">Version';
$n['field'] = '<input class="form-control red" type="text" id="name" name="config[version]" placeholder="" value="' . $this->getConfig('version') . '"/>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');


$error = $this->getConfig('name') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="name" >Name<sup>*</sup></label>';
$n['field'] = '<input class="form-control '.$error.'" type="text" id="name" name="config[name]" placeholder="Bitte ausfüllen" value="' . $this->getConfig('name') . '" required />';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');


$error = $this->getConfig('short_name') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="short_name" >Kurzer Name<sup>*</sup></label>';
$n['field'] = '<input class="form-control '.$error.'" type="text" id="short_name" name="config[short_name]" placeholder="Bitte ausfüllen" value="' . $this->getConfig('short_name') . '" required />';
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


$content .= '</fieldset>';
$content .= ' 
<div class="modal fade" id="name_modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Name
        <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
        </h2>
      </div>
     <div class="modal-body">
            <p><b>Name</b><br/>
            Die Seite kann nicht in einem iFrame eingebettet werden, egal welches die aufrufende Webseite ist.</p>
            <p><b>Kuruzname</b><br/>
            Die Seite kann nur als iFrame eingebettet werden, wenn beide von der gleichen Quellseite stammen.
            <p><b>allow-from uri</b><br/>
            Die Seite lässt sich ausschließlich dann einbetten, wenn die einbettende Seite aus der Quelle uri stammt.</p>
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


$error = $this->getConfig('image512') == '' ? 'error' : '';

// Dateiauswahl Medienpool-Widget
$formElements = [];
$n = [];
$n['label'] = '<label for="REX_MEDIA_1">Bild 512px x 512px</label>';

$n['field'] = '
<div class="rex-js-widget rex-js-widget-media">
	<div class="input-group '.$error.'">
		<input class="form-control" type="text" name="config[image512]" value="' . $this->getConfig('image512') . '" id="REX_MEDIA_1" readonly="readonly">
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
            <p>X-Powered-By kann die verwendete PHP Version zurückgeben und je weniger Infos ein Angreifer hat desto besser! Also: ABSCHALTEN! </p>
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
            <p>X-Powered-By kann die verwendete PHP Version zurückgeben und je weniger Infos ein Angreifer hat desto besser! Also: ABSCHALTEN! </p>
       </div>      
    </div>
  </div>
</div>
';

$content .= '</div>';


// --------
// -------- Display Mode
// --------

if($this->getConfig('display_mode') != '') {
    $content .= '<div class="fieldsetwrapper_pwa green">';
} else {
    $content .= '<div class="fieldsetwrapper_pwa red">';
}


$content .= '<fieldset>';
$content .= '<legend>Display Mode';
$content .= '<a class="help-block rex-note" data-toggle="modal" href="#display_mode_modal">(Mehr Informationen)</a>';
$content .= '</legend>';

$error = $this->getConfig('display_mode') == '' ? 'error' : '';

$formElements = [];
$n = [];
$n['label'] = '<label for="display_mode">Auswahl<sup>*</sup></label>';
$select = new rex_select();
$select->setId('display_mode');
$select->setAttribute('class', 'form-control '.$error.'');
$select->setName('config[display_mode]');
$select->addOption('Bitte wählen', '');
$select->addOption('Standalone', 'Standalone');
$select->addOption('Fullscreen', 'Fullscreen');
$select->addOption('Minimal-UI', 'Minimal-UI');
$select->addOption('Browser', 'Browser');
$select->setSelected($this->getConfig('display_mode'));
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
