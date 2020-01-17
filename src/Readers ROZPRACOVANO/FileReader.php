<?php
namespace Pes\Readers;

use Pes\Readers\FileReaderInterface;
/**
 * Description 
 * 
 * @author vlse2610
 */
/**
 * Objekt slouží ke čtení obsahu souboru.
 */
class FileReader implements FileReaderInterface {
    private $useCache;
    private $cache = array();
    
    protected $jmenoSouboruSCestou;
    protected $dirName;
    protected $baseFileName;
    protected $fileName;
    protected $extension;
    
    protected $contentType;
//----------------------------------------------------   
   
    /**
     * Naplní ->jmenoSouboruSCestou. 
     * @param string $jmenoSouboru úplné jméno souboru, např. 'D:/cesta/adresar/soubor.pripona
     */
    /**
     * Konstruktor. Zadáním parametru je možné povolit užívání cache pro obsah souboru. 
     * @param type $jmenoSouboru 
     * @param type $useMemoryCache
     * @throws InvalidArgumentException
     */
    public function __construct( $jmenoSouboru, $useCache = FALSE) {  
        if (is_readable($jmenoSouboru)) {
            $this->jmenoSouboruSCestou  =  $jmenoSouboru;
            $path_parts = pathinfo($this->jmenoSouboruSCestou);
            $this->baseFileName = $path_parts['basename'];
            $this->dirName = $path_parts['dirname'];
            $this->fileName = $path_parts['filename'];
            $this->extension = $path_parts['extension'];
            $this->contentType = mime_content_type($jmenoSouboru);  //další info http://php.net/manual/en/function.mime-content-type.php
            if (!isset($this->contentType)) {
                throw new InvalidArgumentException('Nepodařilo se rozpoznat MIME type (Content-Type) zadaného souboru '.$jmenoSouboru);
            }
            $this->useCache = $useCache;
        } else {
            throw new InvalidArgumentException('Nelze číst zadaný soubor '.$jmenoSouboru);
        }
    }
   
   /**
    * Načte obsah souboru proměnné a vrací ho jako string. Pokud bylo povoleno užívaní cache, vrací prioritně obsah cache.
    * @return string Načtený obsah souboru
    */ 
    public function getData() { 
        if ($this->useCache AND array_key_exists($this->jmenoSouboruSCestou, $this->cache)) {
            $obsahSouboru = $this->cache[$this->jmenoSouboruSCestou];
        } else {
            $obsahSouboru = file_get_contents($this->jmenoSouboruSCestou);
            if ($this->useCache) {
                $this->cache[$this->jmenoSouboruSCestou] = $obsahSouboru;
            }
        }
        return  $obsahSouboru;
    }
    
    public function getContentType() {
        return $this->contentType;
    }
    
    function getFullFileName() {
        return $this->jmenoSouboruSCestou;
    }

    public function getBaseName() {
        return $this->baseFileName;
    }

    function getDirName() {
        return $this->dirName;
    }

    function getFileName() {
        return $this->fileName;
    }

    function getExtension() {
        return $this->extension;
    }


}
