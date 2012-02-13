<?php

/**
 * Description of File
 *
 * @author pyro
 */
class Bs_Server_File 
{
    public function serve($fileName, $contentType, $outFileName = "", $cacheLife = '30')
    {
        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822,strtotime(" {$cacheLife} day")));
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) 
               && 
          (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($fileName))) {
          // send the last mod time of the file back
          header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($fileName)).' GMT', 
          true, 304);
          
          return;
          
        }
        
        header('Content-type: '.$contentType);
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($fileName)).' GMT');
        $fh = fopen($fileName, "rb");
        $content = fread($fh, filesize($fileName));
        echo $content;
        flush();
        fclose($fh);
    }
    
    public function serveAndExit($fileName, $contentType, $outFileName = "", $cacheLife = '30')
    {
        $this->serve($fileName, $contentType, $outFileName, $cacheLife);
        exit();
    }
}

?>
