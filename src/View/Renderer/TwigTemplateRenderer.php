<?php

namespace Pes\View\Renderer;

use Twig_Environment;

/**
 * Renderer používající pro generování obsahu template objekt, který jako šablony používá Twig šablony.
 * Je to dekorátor pro Twig_Environment template objekt.
 *
 * @author pes2704
 */
class TwigTemplateRenderer extends TemplateRendererAbstract implements TemplateRendererInterface {

    /**
     * Vrací výstup získaný ze zadaného template objektu.
     * Metoda implementuje metodu rozhraní render(). Volá metodu render() Twig objektu.
     *
     * @param mixed $data Pole nebo objekt Traversable
     * @return string
     */
    public function render($data=NULL) {
        return $this->template->render($data);
    }
}
