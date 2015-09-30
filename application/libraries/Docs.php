<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Docs {

    var $disallowed_filetypes;

    function __construct() {
        $this->disallowed_filetypes = array('asp', 'aspx', 'cgi', 'pl', 'SASS', 'exe', 'py', 'pList', 'ds_store', 'bsp', 'arch00', 'xap', 'php', 'jar', 'msi', 'deb', 'apk', 'unity3d', 'vmt', 'pm', 'dat', 'htaccess', 'htpasswd', 'html', 'htm');
    }

    function get_docs_in_folder($folder) {
        $not_allowed = $this->disallowed_filetypes;
        $files_not_allowed = array('..','.');
        $array =
            array_filter(
                scandir($folder),
                function($file) use ($not_allowed, $files_not_allowed) {
                    return (
                        !in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $not_allowed)
                        &&
                        !in_array($file,$files_not_allowed)
                    );
                }
            );
        sort($array);
        $ret = array();
        foreach($array as $doc) {
            $ret[] = array(
                'name' => $doc,
                'ext' => pathinfo($doc, PATHINFO_EXTENSION),
                'size' => $this->format_size(filesize($folder.'/'.$doc)),
                'modified' => 'Last modified: '.date('H:i d/m/y', filemtime($folder.'/'.$doc))
            );
        }
        return $ret;
    }

    function format_size($size) {
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) { return('n/a'); } else {
        return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
    }
}