<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer;

use Pes\View\Template\ImplodeTemplateInterface;

/**
 * ImplodeRenderer pouze zřetězí data s použitím separátoru zadaného v konstruktoru, použije php funkci implode().
 * Má svůj interface. Nemplementuje RendereInterface!!
 *
 * @author pes2704
 */
class ImplodeRenderer implements ImplodeRendererInterface {

    /**
     * @var TemplateInterface
     */
    private $template;

    /**
     * Zřetězí data jako string složený z hodnot oddělených (slepených) separátorem.
     *
     * @param ImplodeTemplateInterface $template
     * @param \Traversable $data Array nebo objekt Traversable.
     * @return string
     * @throws \UnexpectedValueException
     */
    public function render(ImplodeTemplateInterface $template, $data=NULL) {
        $separator = $template->getSeparator();
        if ($data) {
            if (is_array($data)) {
                $str = implode($separator, $data);
            } elseif ($data instanceof \Traversable) {
                foreach ($array as $value) {
                    $arr[] = $value;
                }
                $str = implode($separator, $arr);
            } else {
                throw new \UnexpectedValueException("Data musí být array nebo Traversable.");
            }
        }
        return $str ?? '';
    }
}
