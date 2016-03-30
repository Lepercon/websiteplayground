<?php   

class ControllersTest extends PHPUnit_Framework_TestCase
{
    private $CI;

    public function setUp()
    {
        $this->CI =& get_instance();
    }
    
    private function list_files($dir){
        $files = array_diff(scandir($dir), array('..', '.'));
        $new_files = array();
        /*foreach($files as $k=>$f){
            if(is_dir($dir.$f)){
                unset($files[$k]);
                $new_files = array_merge($new_files, $this->list_files($dir.$f.'/'));
            }
        }*/
        foreach($files as $k=>$f){
            $files[$k] = $dir.$files[$k];
        }
        return array_merge($files, $new_files);
    }
    
    public function testValidControllers()
    {
        $files = $this->list_files(APPPATH.'controllers/');
        
        $re = "/([a-zA-Z_]*)\\.php/"; 
        $matches = NULL;
        foreach($files as $f){
            if(preg_match($re, $f, $matches)){
                $this->assertTrue(file_exists($f), $f.' does not exist');
                require_once $f;
                $this->assertTrue(class_exists(ucfirst($matches[1])), $matches[1].' is not loadable');
            }
        }
    }
    
    public function testValidModels(){
        $files = $this->list_files(APPPATH.'models/');
        
        $re = "/([a-zA-Z_]*)\\.php/"; 
        $matches = NULL;
        foreach($files as $f){
            if(preg_match($re, $f, $matches)){
                $this->assertTrue(file_exists($f), $f.' does not exist');
                $this->CI->load->model($matches[1]);
                $this->assertTrue(class_exists(ucfirst($matches[1])), $matches[1].' is not loadable');
            }
            
        }
    }
    
    
}

?>