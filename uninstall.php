<?php

$addon = rex_addon::get('pwa');
$successMsg = array();

rex_dir::delete($addon->getDataPath());

if(file_exists('../manifest.json')){
    unlink('../manifest.json');
}
if(file_exists('../service-worker.js')){
    unlink('../service-worker.js');
}

$mmtypes = explode(',',$addon->getConfig('image_formats'));

$sql = rex_sql::factory();

foreach ($mmtypes as $size) {

    $sql->setQuery('select id from '.rex::getTable('media_manager_type').' where name="pwa'.$size.'" LIMIT 1');
    if( $sql->getRows() ) {
        $id = $sql->getValue( 'id' );
        $sql->setTable( rex::getTable('media_manager_type') );
        $sql->setWhere( 'id='.$id );
        $sql->delete();
        $sql->setTable( rex::getTable('media_manager_type_effect') );
        $sql->setWhere( 'type_id='.$id );
        $sql->delete();
    }

}