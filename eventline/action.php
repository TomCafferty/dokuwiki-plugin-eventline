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
require_once ('getXmlData.php');
require_once (DOKU_INC.'inc/parserutils.php');

class action_plugin_eventline extends DokuWiki_Action_Plugin {

    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(&$controller) {
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
        $key = 'keywords';
        
        $metadata = p_get_metadata($ID, $key, false);
        
        // keyword timeline used to include timeline javascript files
        if ($metadata == 'timeline')  {
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

        // our event?
        if ($ACT != 'export_timeline' ) return false;

        // check user's rights
        if ( auth_quickaclcheck($ID) < AUTH_READ ) return false;

        // it's ours, no one else's
        $event->preventDefault();

        // get page data
        $html = pullInXmlData($ID);

        // write to xml file
        $fp = fopen(DOKU_INC . 'data/pages/'. str_replace(":", "/", $ID) . '.xml', 'w');
        fwrite($fp, $html);
        fclose($fp);
        
        // remain on current page
        header("HTTP/1.1 204 No Content"); 
        exit();        
    }
}