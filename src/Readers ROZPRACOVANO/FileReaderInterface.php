<?php
namespace Pes\Readers;

use Pes\Readers\ReaderInterface;

/**
 *
 * @author pes2704
 */
interface FileReaderInterface extends ReaderInterface {
    public function getFullFileName();
    public function getDirName();
    public function getBaseName();
    public function getFileName();
    public function getExtension();
}
