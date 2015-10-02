<?php
    exit();
    $hookSecret = 'cAZQB2VJs6NKF6k5seWhvaxk';  # set NULL to disable check

    set_error_handler(function($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
    });

    set_exception_handler(function($e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "Error on line {$e->getLine()}: " . htmlSpecialChars($e->getMessage());
        die();
    });

    $rawPost = NULL;
    if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
            throw new \Exception("HTTP header 'X-Hub-Signature' is missing.");
    } elseif (!extension_loaded('hash')) {
            throw new \Exception("Missing 'hash' extension to check the secret code validity.");
    }

    list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
    if (!in_array($algo, hash_algos(), TRUE)) {
            throw new \Exception("Hash algorithm '$algo' is not supported.");
    }

    $rawPost = file_get_contents('php://input');
    if ($hash !== hash_hmac($algo, $rawPost, $hookSecret)) {
            throw new \Exception('Hook secret does not match.');
    }

    $json = json_decode(file_get_contents('php://input'), true);
    $files['download'] = array_merge($json['head_commit']['added'], $json['head_commit']['modified']);
    $files['delete'] = $json['head_commit']['removed'];
    
    try{ rename('offline_fully', 'offline_full'); }catch (Exception $e){}
   
    $contents_url = 'https://api.github.com/repos/butlerjcr/butlerjcr.co.uk/contents/';
    //$files = array('README.md', 'phpinfo.php', 'offline/logo.png');
    $token = '?access_token=954697c93c9a0871b824aa369ff4947cb41a7cbb';
    
    
    foreach($files['download'] as $f){
        $url = $contents_url.$f.$token;
        //var_dump($f);
        //$fh = fopen(basename($url), "wb");
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Test App');
        $json = curl_exec($ch);
        curl_close($ch);
        //var_dump($json);
        $data = json_decode($json, true);
        $url = $data['download_url'];
        
        $path = $f;
        try{
            mkdir(dirname($path), 0755, true);
        }catch (Exception $e){
            
        }
        var_dump($path);
        $fh = fopen($path, "w");
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Test App');
        curl_exec($ch);
        curl_close($ch);
        
    }
    
    foreach($files['delete'] as $f){
        unlink($f);
    }
    
    try{ rename('offline_full', 'offline_fully'); }catch (Exception $e){}
?>