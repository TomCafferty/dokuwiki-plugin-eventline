<?php
/**
 * Timeline Action Plugin -  Get Wiki page with data.
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * Gets page content and returns it as text
 *
 * @author   Jerome Jangle <http://maestric.com/>
 * @author   Tom Cafferty <tcafferty@glocalfocal.com>
 * @param    string $dokuPageId pagename containing timeline data 
 * @returns  string $html       page content as text
 *
 */

function pullInXmlData ($dokuPageId) {
        
    /* Initialization */
    define('DOKU_PATH', realpath(dirname(__FILE__)) . '/../../../');

    // don't refresh caches
    unset($_REQUEST['purge']); 
    require_once DOKU_PATH . '/inc/cache.php';
    
    // from id parameter, build text file path
    $pagePath = DOKU_PATH . '/data/pages/'. str_replace(":", "/", $dokuPageId) . '.txt';
    
    // get cached instructions for that file
    $cache = new cache_instructions($dokuPageId, $pagePath); 
    if ($cache->useCache()){ 
        $instructions = $cache->retrieveCache(); 
    } else { 
        $instructions = p_get_instructions(io_readfile($pagePath)); 
        $cache->storeCache($instructions); 
    } 
    
    // create plain text renderer
    require_once 'plain.php';
    $renderer = new Doku_Renderer_plain();
            
    foreach ( $instructions as $instruction ) {
        call_user_func_array(array(&$renderer, $instruction[0]),$instruction[1]);
    }
    
    // get rendered html
    $html = $renderer->doc;
    
    return $html;
}
