<?php

/**
 * Timeline Action Plugin:   Plain Text Renderer Component.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     mukl
 * @author     Tom Cafferty <tcafferty@glocalfocal.com>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');

if ( !defined('DOKU_LF') ) {
    define ('DOKU_LF',"\n");
}

if ( !defined('DOKU_TAB') ) {
    define ('DOKU_TAB',"\t");
}

require_once DOKU_INC . 'inc/parser/renderer.php';
require_once DOKU_INC . 'inc/parser/xhtml.php';

/**
 * The Renderer
 * Provides plain text output for xml file input to Timeline app. 
 */
class Doku_Renderer_plain extends Doku_Renderer_xhtml {
    
  var $base_url;

  function set_base_url($url) {
    	$this->base_url = $url;
  }

  function getFormat() {
    return 'plain';
  }

  function document_start() {
  }

  function document_end() {
  }
 
  function cdata($text) {
    $this->doc .= ' ' .$text . ' ';
  }

  function header($text, $level, $pos) {
    $this->doc .=  DOKU_LF . $text . DOKU_LF;
  }

  function preformatted($text) {
    $this->doc .= ' ' . $text . ' '. DOKU_LF;
  }

  function file($text) {
    $this->doc .= ' ' . $text . ' '. DOKU_LF;
  }
  
  function p_open() {
  }

  function p_close() {
  }

  function code($text, $language = NULL) {
    $this->doc .= ' ' . $text . ' '. DOKU_LF;
  }

  function acronym($acronym) {
    $this->doc .= ' ' . $acronym . ' ';
  }

  function smiley($smiley) {
    $this->doc .= ' ' . $smiley . ' ';
  }

  function entity($entity) {
    $this->doc .= ' ' . $entity . ' ';
  }

  function camelcaselink($link) {
    $this->internallink($link,$link);
  }

  function locallink($hash, $name = NULL){
    $this->doc .= ' ' . $hash . ' ';
  }
 
  function externallink($url, $name = NULL) {
    $this->doc .= ' ' . html_entity_decode($url, ENT_NOQUOTES) . ' ';
  }

  function interwikilink($match, $name = NULL, $wikiName, $wikiUri) {
    $this->doc .= ' ' . $match . ' ';
  }

  function emaillink($address, $name = NULL) {
    $this->doc .= ' ' . $address . ' ';
  }

  function internalmedia ($src, $title=NULL, $align=NULL, $width=NULL,
                            $height=NULL, $cache=NULL, $linking=NULL) {
        global $ID;
        list($src,$hash) = explode('#',$src,2);
        resolve_mediaid(getNS($ID),$src, $exists);

        $noLink = false;
        $render = ($linking == 'linkonly') ? false : true;
        $link = $this->getMediaLinkConf($src, $title, $align, $width, $height, $cache, $render);

        list($ext,$mime,$dl) = mimetype($src,false);
        if($hash) $link['url'] .= '#'.$hash;

        //output formatted
        $this->doc .= $link['name'];
    }

  function externalmedia ($src, $title=NULL, $align=NULL, $width=NULL,
                        $height=NULL, $cache=NULL, $linking=NULL) {
    $this->doc .= ' ' . $src . ' ';
  }
  
  function internallink($id, $name = NULL, $search=NULL,$returnonly=false)
	{
        global $conf;
        global $ID;

		$default = $id;

        // now first resolve and clean up the $id
        resolve_pageid(getNS($ID),$id,$exists);
        $name = $this->_getLinkTitle($name, $default, $isImage, $id);
        if ( !$isImage ) {    
            $class='wikilink1';
        } else {
            $class='media';
        }

        // don't keep hash anchor
		$hash = "";

        //prepare for formating
        $link['target'] = $conf['target']['wiki'];
        $link['style']  = '';
        $link['pre']    = '';
        $link['suf']    = '';

        // highlight link to current page
        if ($id == $ID) {
            $link['pre']    = '<span class="curid">';
            $link['suf']    = '</span>';
        }
        $link['more']   = '';
        $link['class']  = $class;

		// make links
		if ($class = 'wikilink2') {
			$link['url']    =  $this->base_url . $id;
		}
		else {
	        $link['url']    =  $this->base_url . $id;			
		}
        $link['name']   = $name;
        $link['title']  = $id;

        //add search string
        if($search){
            ($conf['userewrite']) ? $link['url'].='?s=' : $link['url'].='&amp;s=';
            $link['url'] .= rawurlencode($search);
        }

        //keep hash
        if($hash) $link['url'].='#'.$hash;

		//output formatted
        if($returnonly){
            return $this->_formatLink($link);
        }else{
            $this->doc .= $this->_formatLink($link);
        }
    }
    
    //----------------------------------------------------------
    // Utils

    /**
     * _getMediaLinkConf is a helperfunction to internalmedia() and externalmedia()
     * which returns a basic link to a media.
     *
     * @author Pierre Spring <pierre.spring@liip.ch>
     * @author Tom Cafferty  <tcafferty@glocalfocal.com>
     * @param string $src
     * @param string $title
     * @param string $align
     * @param string $width
     * @param string $height
     * @param string $cache
     * @param string $render
     * @access protected
     * @return array
     */
    function getMediaLinkConf($src, $title, $align, $width, $height, $cache, $render)
    {
        global $conf;

        $link = array();
        $link['class']  = '';
        $link['style']  = '';
        $link['pre']    = '';
        $link['suf']    = '';
        $link['more']   = '';
        $link['target'] = '';
        $link['name']   = $this->timelineMedia($src, $title, $align, $width, $height, $cache, $render);

        return $link;
    }

    /**
     * Renders internal and external media
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @author Tom Caferty <tcafferty@glocalfocal.com>
     */
    function timelineMedia ($src, $title=NULL, $align=NULL, $width=NULL,
                      $height=NULL, $cache=NULL, $render = true) {

        $ret = '';

        list($ext,$mime,$dl) = mimetype($src);
        if(substr($mime,0,5) == 'image'){
            // first get the $title
            if (!is_null($title)) {
                $title  = $this->_xmlEntities($title);
            }elseif($ext == 'jpg' || $ext == 'jpeg'){
                //try to use the caption from IPTC/EXIF
                require_once(DOKU_INC.'inc/JpegMeta.php');
                $jpeg =new JpegMeta(mediaFN($src));
                if($jpeg !== false) $cap = $jpeg->getTitle();
                if($cap){
                    $title = $this->_xmlEntities($cap);
                }
            }
            if (!$render) {
                // if the picture is not supposed to be rendered
                // return the title of the picture
                if (!$title) {
                    // just show the sourcename
                    $title = $this->_xmlEntities(basename(noNS($src)));
                }
                return $title;
            }
            //add image tag
            $ret .= 'image="'.ml($src,array('w'=>$width,'h'=>$height,'cache'=>$cache)).'"';

            // make left/right alignment for no-CSS view work (feeds)
            if($align == 'right') $ret .= ' align="right"';
            if($align == 'left')  $ret .= ' align="left"';

            if ($title) 
                $ret .= ' title="' . $title . '"';

            if ( !is_null($width) )
                $ret .= ' width="'.$this->_xmlEntities($width).'"';

            if ( !is_null($height) )
                $ret .= ' height="'.$this->_xmlEntities($height).'"';


        }elseif($mime == 'application/x-shockwave-flash'){
            if (!$render) {
                // if the flash is not supposed to be rendered
                // return the title of the flash
                if (!$title) {
                    // just show the sourcename
                    $title = basename(noNS($src));
                }
                return $this->_xmlEntities($title);
            }

            $att = array();
            $att['class'] = "media$align";
            if($align == 'right') $att['align'] = 'right';
            if($align == 'left')  $att['align'] = 'left';
            $ret .= html_flashobject(ml($src,array('cache'=>$cache),true,'&'),$width,$height,
                                     array('quality' => 'high'),
                                     null,
                                     $att,
                                     $this->_xmlEntities($title));
        }elseif($title){
            // well at least we have a title to display
            $ret .= $this->_xmlEntities($title);
        }else{
            // just show the sourcename
            $ret .= $this->_xmlEntities(basename(noNS($src)));
        }

        return $ret;
    }


}

