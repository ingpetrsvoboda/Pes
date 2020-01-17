<?php
namespace Pes\Readers;

/**
 *
 * @author pes2704
 */
interface ReaderInterface {
    
    public function getData();
    
    /**
     * Každý reader musí vracet Content-Type. Content-Type (také MIME type souboru) je dvojice řetězců oddělených lomítkem, např text/html.
     * I readery, které svá data nenačítají ze souboru musí Content-Type vracet.
     */
    public function getContentType();
}
