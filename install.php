<?php

$addon = rex_addon::get('pwa');

if (!$addon->hasConfig()) {
    $addon->setConfig('image_formats', '16,32,64,192,196,256,512,1024');
}

$mmtypes = explode(',',$addon->getConfig('image_formats'));

$sql = rex_sql::factory();

foreach ($mmtypes as $size) {

    $sql->setQuery('select id from '.rex::getTable('media_manager_type').' where name="pwa'.$size.'" LIMIT 1');
    if( $sql->getRows() ) {
        $successMsg[] = 'Der MediaManager Typ <b>pwa'.$size.'</b> existiert schon und wurde nicht angelegt.';
    } else {
        $sql->setTable( rex::getTable('media_manager_type'));
        $sql->setValue( 'name', 'pwa'.$size );
        $sql->addGlobalCreateFields();
        $sql->addGlobalUpdateFields();
        $sql->insert();
        $mm_type_id = $sql->getLastId();
        $mm_action_effect = true;
        $successMsg[] = 'Der MediaManager Typ <b>pwa16</b> wurde angelegt';

        $sql->setTable( rex::getTable('media_manager_type_effect' ));
        $sql->setValue( 'type_id', $mm_type_id );
        $sql->setValue( 'effect', 'resize' );
        $sql->setValue( 'parameters', '{"rex_effect_resize":{"rex_effect_resize_width":"'.$size.'","rex_effect_resize_height":"'.$size.'","rex_effect_resize_style":"exact","rex_effect_resize_allow_enlarge":"enlarge"}}' );
        $sql->setValue( 'priority', '1' );
        $sql->addGlobalUpdateFields();
        $sql->addGlobalCreateFields();
        $sql->insert();
        $successMsg[] = 'Der MediaManager Effekt <b>Bild: Skalieren</b> für den Typen <b>pwa16</b> wurde angelegt.';
    }

}


if( $successMsg ) {
    $this->setProperty('successmsg', '<ul><li>'.implode('</li><li>',$successMsg).'</ul>' );
}
