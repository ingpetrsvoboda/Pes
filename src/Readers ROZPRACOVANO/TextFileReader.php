<?php
namespace Pes\Readers;

use Pes\Readers\FileReader;
use Pes\Readers\TextReaderInterface;

/**
 * Description of TextFileReader
 *
 * @author pes2704
 */
class TextFileReader extends FileReader implements TextReaderInterface {
    
    private $encoding;
    
    public function __construct($jmenoSouboru, $encoding='UTF-8') {
        parent::__construct($jmenoSouboru);
        $typeParts = explode('/', $this->contentType);
        if (!strpos('text', $typeParts[0]) === 0) {
            throw new InvalidArgumentException('Reader je pro obsah typu text a rozpoznaný MIME type (Content-Type) zadaného souboru není text, je '.$this->contentType);
        }
        $this->encoding = $encoding;
    }
    
    /**
     *
     * https://github.com/neitanod/forceutf8
     * http://stackoverflow.com/questions/910793/detect-encoding-and-make-everything-utf-8
     */
    public function getData() {
        $str = parent::getData();
        if (!\mb_detect_encoding($str, $this->encoding, true)) {
            throw new UnexpectedValueException('Přečtený obsah souboru není v kódování '.$this->encoding.'. Soubor '.$this->getBaseName());
        }
        return $str;
    }
    
    /**
     * Tento reader čte jen utf8 kódované soubory
     * https://github.com/neitanod/forceutf8
     * http://stackoverflow.com/questions/910793/detect-encoding-and-make-everything-utf-8
     */
    public function getCharset() {
        return 'utf8';
    }
}
