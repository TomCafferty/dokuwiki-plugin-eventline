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
        
       if (isset($data['hotzone']) && $data['hotzone']=='on') 
        $hotzone = 1;
       else
        $hotzone = 0;
        
       $hzStart = $data['hzStart'];
       $hzStart2 = $data['hzStart2'];
       $hzStart3 = $data['hzStart3'];
       $hzEnd = $data['hzEnd'];
       $hzEnd2 = $data['hzEnd2'];
       $hzEnd3 = $data['hzEnd3'];
       $hzMagnify = $data['hzMagnify'];
       $hzMagnify2 = $data['hzMagnify2'];
       $hzMagnify3 = $data['hzMagnify3'];
       $hzUnit = $data['hzUnit'];
       $hzUnit2 = $data['hzUnit2'];
       $hzUnit3 = $data['hzUnit3'];
              
      // Get file name ($dataFile) and full url path 
      $ns = $INFO['namespace'];
      if (strpos($ns, ':') == false) $ns = $ns . ':';   
      $dataFile = $ID.':' . $data['file'];
      $filePath = DOKU_URL . 'lib/plugins/eventline/getData.php?id='.urlencode($dataFile);

      // Set timeline div & class for css styling and jsvascript id
	  $R->doc .='<div id="eventlineplugin__timeline" class="eventlineplugin__class" style="height:'.$height.';"></div>';
	  
	  // Add a link to the data file for dokuwiki editing (.txt) version if user has write access
	  $showlink = $this->getConf('showlink');      
      $info_perm     = auth_quickaclcheck($dataFile);
      $info_filepath = fullpath(wikiFN($dataFile));
      $info_writable = (is_writable($info_filepath) && ($info_perm >= AUTH_EDIT));
	  if($info_writable || ($showlink==1))
	  $R->doc .='<div id="eventlineplugin__data"> Go to <a title="' . $dataFile .'" class="wikilink1" href="' . wl($dataFile) . '">'.$data['file'].'</a> data</div>';

	  // Add a div for timeline filter controls if selected
	  if ($controls==1){
		$R->doc .='<div class="eventlineplugin__controls" id="eventlineplugin__controls"></div>';
	  }

	  // onload invoke timeline javascript 
	  $R->doc .='<script>window.onLoad = onLoad("'.$filePath.'" , '.$bubbleHeight.', '.$bubbleWidth.', "'.$mouse.'", "'.$center.'", "'
	  .$controls.'", "'.$bandPos.'", "'.$detailPercent.'", "'.$overPercent.'", "'.$detailPixels.'", "'.$overPixels.'", "'.$detailInterval.'", 
	  "'.$overInterval.'", "'.$hotzone.'", "'.$hzStart.'", "'.$hzEnd.'", "'.$hzMagnify.'", "'.$hzUnit.'", "'.$hzStart2.'", "'.$hzEnd2.'", "'.$hzMagnify2.'", "'.$hzUnit2.'", "'.$hzStart3.'", "'.$hzEnd3.'", "'.$hzMagnify3.'", "'.$hzUnit3.'");</script>' . "\n";
	  $R->doc .='<script>window.onResize=onResize();</script> ';
	  return true;
    }
}