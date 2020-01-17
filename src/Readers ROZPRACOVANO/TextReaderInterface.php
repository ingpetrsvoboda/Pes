<?php
namespace Pes\Readers;

use Pes\Readers\FileReaderInterface;

/**
 *
 * @author pes2704
 */
interface TextReaderInterface extends FileReaderInterface{
    
    /**
     * Reader který vrací textový obsah (Content-Type např. text/html, text/plain, tedy všechny typy text) musí vracet 
     * kódování obsahu (vraceného metodou getData(). Rozhodně doporučuji vracet defaultně 'utf8'. Platné jsou všechny znakové sady registrované
     * IANA, ale sadu musí rozpoznat prohlížeč nebo mail klient.
     */
    public function getCharset();
}
