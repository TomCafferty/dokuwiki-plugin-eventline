<?php
/**
 * Timeline Action Plugin
 *
 *  Provides a wiki timeline
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Tom Cafferty <tcafferty@glocalfocal.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'action.php';

class action_plugin_eventline extends DokuWiki_Action_Plugin {

    function getInfo() {
        return array(
            'author' => 'Tom Cafferty',
            'email'  => 'tcafferty@glocalfocal.com',
            'date'   => '2011-09-30',
            'name'   => 'eventline',
            'desc'   => 'Integrate simile timeline with dokuwiki',
            'url'    => 'http://www.dokuwiki.org/plugin:eventline'
        );
    }

    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'eventline_hookjs');
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'convert',array());
    }

    /**
     * Hook js script into page headers.
     *
     * @author Tom Cafferty <tcafferty@glocalfocal.com>
     */
    function eventline_hookjs(&$event, $param) {
        global $INFO;
        global $ID;
        
        // metadata check to include javascript files if needed
        if (p_get_metadata($ID, 'plugin eventline')) {
            $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            '_data'   => 'Timeline_urlPrefix="'.DOKU_BASE.'lib/plugins/eventline/timeline_js/";');
            $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            '_data'   => "Timeline_parameters='bundle=true';");
            $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            '_data'   => '',
                            'src'     => DOKU_BASE."lib/plugins/eventline/timeline_ajax/simile-ajax-api.js");
            $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            '_data'   => '',
                            'src'     => DOKU_BASE."lib/plugins/eventline/timeline_js/timeline-api.js");
            $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            '_data'   => '',
                            'src'     => DOKU_BASE."lib/plugins/eventline/timeline.js");
            $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            '_data'   => "SimileAjax.History.enabled=false;");
       }
    }
    
    /**
     * convert script for xml plain text file output.
     *
     * @author Tom Cafferty <tcafferty@glocalfocal.com>
     */
    function convert(&$event, $param) {
        global $ACT;
        global $ID;
        global $conf;
        $key = 'keywords';

        // our event?
        if ($ACT != 'export_timeline' ) return false;

        // check user's rights
        if ( auth_quickaclcheck($ID) < AUTH_READ ) return false;

        // it's ours, no one else's
        require_once ('getXmlData.php');
        $event->preventDefault();
        
       $wikihtml = '';
       $metadata = p_get_metadata($ID, $key, false);
       if (strpos($metadata, 'eventline_html') !== false) {
         $wikihtml = 1; }
       elseif (strpos($metadata, 'eventline_nohtml') !== false) {
         $wikihtml = 0; }
       else {
        $wikihtml = $this->getConf('wikihtml'); }

        // get page data
        if (strpos($metadata, 'eventline_fr') !== false) {
            setlocale(LC_CTYPE, 'fr_FR');
            $html = iconv('UTF-8', 'ASCII//TRANSLIT', pullInXmlData($ID, $wikihtml));
            $html = str_ireplace("'", "&#039;", $html);
        } else {
            $html = pullInXmlData($ID, $wikihtml);
        }

        // write to xml file
        $fp = fopen(DOKU_INC . 'data/pages/'. str_replace(":", "/", $ID) . '.xml', 'w');
        fwrite($fp, $html);
        fclose($fp);
        
        // remain on current page
        header("HTTP/1.1 204 No Content"); 
        exit();        
    }
}
