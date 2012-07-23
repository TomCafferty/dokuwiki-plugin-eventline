<?php
/**
 * Timeline Action Plugin
 *
 *  Provides a wiki timeline
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Dan Kreiser <dan.kreiser@gmail.com>
 * @author     Tom Cafferty <tcafferty@glocalfocal.com>
 */
if(!defined('DOKU_INC')) define('DOKU_INC',(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_eventline extends DokuWiki_Syntax_Plugin {

    function getInfo() {
        return array(
            'author' => 'Tom Cafferty',
            'email'  => 'tcafferty@glocalfocal.com',
            'date'   => '2011-12-29',
            'name'   => 'eventline',
            'desc'   => 'Integrate simile timeline with dokuwiki',
            'url'    => 'http://www.dokuwiki.org/plugin:eventline'
        );
    }
    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }

    function getPType(){
        return 'block';
    }

    /**
     * Where to sort in?
     */
    function getSort(){
        return 160;
    }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
      $this->Lexer->addSpecialPattern('<eventline>.*?</eventline>',$mode,'plugin_eventline');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler){
        parse_str($match, $return);   
        return $return;
    }

/**
 *
 * Create timeline output 
 *
 * @author   Tom Cafferty <tcafferty@glocalfocal.com>
 *
 */
    function render($mode, &$R, $data) {
      global $INFO;
      global $ID;
      global $conf;
      if($mode != 'xhtml') return false;
      if ($_SERVER['SERVER_NAME'] == 'localhost') {
          define('TL_ROOT', '/small_gfmodx');
      } else {
          define('TL_ROOT', '');
      }     
      // Initialize settings from user input or conf file
      if (isset($data['bubbleMaxHeight'])) 
        $bubbleHeight = $data['bubbleMaxHeight'];
      else
        $bubbleHeight = $this->getConf('bubbleMaxHeight');
        
      if (isset($data['bubbleWidth'])) 
        $bubbleWidth = $data['bubbleWidth'];
      else
        $bubbleWidth = $this->getConf('bubbleWidth');      
        
      if (isset($data['height'])) 
        $height = $data['height'];
      else
        $height = $this->getConf('height');
        
      if (isset($data['mouse'])) 
        $mouse = $data['mouse'];
      else
        $mouse = $this->getConf('mouse');
        
       if (isset($data['center'])) 
        $center = $data['center'];
      else
        $center = $this->getConf('center');
        
       if (isset($data['controls'])) 
        $controls = $data['controls'];
      else
        $controls = $this->getConf('controls');
        
       if (isset($data['bandPos'])) 
        $bandPos = $data['bandPos'];
      else
        $bandPos = $this->getConf('bandPos');
        
       if (isset($data['detailPercent'])) 
        $detailPercent = $data['detailPercent'];
      else
        $detailPercent = $this->getConf('detailPercent');
        
       if (isset($data['overPercent'])) 
        $overPercent = $data['overPercent'];
      else
        $overPercent = $this->getConf('overPercent');
        
       if (isset($data['detailPixels'])) 
        $detailPixels = $data['detailPixels'];
      else
        $detailPixels = $this->getConf('detailPixels');
        
       if (isset($data['overPixels'])) 
        $overPixels = $data['overPixels'];
      else
        $overPixels = $this->getConf('overPixels');
        
       if (isset($data['detailInterval'])) 
        $detailInterval = $data['detailInterval'];
      else
        $detailInterval = $this->getConf('detailInterval');
        
       if (isset($data['overInterval'])) 
        $overInterval = $data['overInterval'];
      else
        $overInterval = $this->getConf('overInterval');
              
      // Get file name ($dataFile) and full url path 
      $ns = $INFO['namespace'];
      if (strpos($ns, ':') == false) $ns = $ns . ':';   
      $dataFile = $ID.':' . $data['file'];
	  if($ID == NULL)
          $filePath = 'http://'.$_SERVER['SERVER_NAME'].'/'. TL_ROOT. '/assets/files/timelines'. str_replace(":", "/", $dataFile) . '.xml';
	  else
          $filePath = DOKU_URL . 'data/pages/'. str_replace(":", "/", $dataFile) . '.xml';

      // Set timeline div & class for css styling and jsvascript id
	  $R->doc .='<div id="eventlineplugin__timeline" class="eventlineplugin__class" style="height:'.$height.';"></div>';
	  
	  // Add a link to the data file for dokuwiki editing (.txt) version
	  $R->doc .='<div id="eventlineplugin__data"> Go to <a title="' . $dataFile .'" class="wikilink1" href="doku.php?id=' . $dataFile . '">'.$data['file'].'</a> data</div>';

	  // Add a div for timeline filter controls if selected
	  if ($controls=='on'){
		$R->doc .='<div class="eventlineplugin__controls" id="eventlineplugin__controls"></div>';
	  }

	  // onload invoke timeline javascript 
	  $R->doc .='<script> window.onload = onLoad("'.$filePath.'" , '.$bubbleHeight.', '.$bubbleWidth.', "'.$mouse.'", "'.$center.'", "'
	  .$controls.'", "'.$bandPos.'", "'.$detailPercent.'", "'.$overPercent.'", "'.$detailPixels.'", "'.$overPixels.'", "'.$detailInterval.'", "'.$overInterval.'");   </script>';	  
	  $R->doc .='<script> window.onresize=onResize(); </script> ';
	  return true;
    }
}