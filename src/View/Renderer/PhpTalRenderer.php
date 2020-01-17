<?php

namespace Pes\View\Renderer;

use \PHPTAL;

/**
 * Renderer používající pro generování obsahu template objekt, který jako šablony používá PHPTAL šablony.
 * Je to dekorátor pro PHPTAL template objekt.
 *
 * @author pes2704
 */
class PhpTalRenderer extends TemplateRendererAbstract implements TemplateRendererInterface {

    /**
     * Vrací výstup získaný ze zadaného template objektu.
     * Metoda implementuje metodu rozhraní render(). Volá metodu execute() PHPTAL objektu.
     *
     * @param mixed $data Pole nebo objekt Traversable nebo Closure, kterí vrací pole nebo objekt Traversable
     * @return string
     */
    public function render($data=NULL) {
        if ($data) {
            foreach($data as $klic => $hodnota) {
                $this->template->$klic = $hodnota;
            }
        }
        return $this->template->execute();
    }
}
