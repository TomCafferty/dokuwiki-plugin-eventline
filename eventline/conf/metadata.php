<?php
$meta['bubbleMaxHeight'] = array('numeric');
$meta['bubbleWidth']     = array('numeric');
$meta['height']          = array('string');
$meta['mouse']           = array('multichoice','_choices' => array('scroll','zoom','default'));
$meta['center']          = array('string');
$meta['controls']        = array('onoff');
$meta['bandPos']         = array('multichoice','_choices' => array('default','reverse'));
$meta['detailPercent']   = array('string');
$meta['overPercent']     = array('string');
$meta['detailPixels']    = array('numeric');
$meta['overPixels']      = array('numeric');
$meta['detailInterval']  = array('multichoice','_choices' => array('MILLISECOND', 'SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR', 'DECADE', 'CENTURY', 'MILLENNIUM', 'EPOCH', 'ERA')); 
$meta['overInterval']    = array('multichoice','_choices' => array('MILLISECOND', 'SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR', 'DECADE', 'CENTURY', 'MILLENNIUM', 'EPOCH', 'ERA')); 