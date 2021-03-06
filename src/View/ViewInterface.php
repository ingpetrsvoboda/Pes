<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View;

use Pes\View\Renderer\RendererInterface;
use Psr\Container\ContainerInterface;

/**
 *
 * @author pes2704
 */
interface ViewInterface {

    /**
     * Vytvoří textový obsah.
     * @param type $data
     * @return string
     */
    public function getString($data=NULL);

    public function __toString();

    /**
     * Nastaví renderer. Pokud je nastaven, je použit při renderování přednostně před rendererem z kontejneru nebo default rendererem templaty.
     * @param RendererInterface $renderer
     * @return \Pes\View\ViewInterface
     */
    public function setRenderer(RendererInterface $renderer): ViewInterface;

    /**
     * Nastaví objekt renderer kontejner.
     *
     * @param ContainerInterface $rendererContainer
     * @return \Pes\View\ViewInterface
     */
    public function setRendererContainer(ContainerInterface $rendererContainer): ViewInterface;

    /**
     * Nastaví jméno služby renderer kontejneru, která musí vracet renderer.
     * Pokud je nastaveno toto jméno služby, použije se renderer vrácená tenderer kontejnerem pro renderování.
     *
     * @param $rendererName
     * @return ViewInterface
     */
    public function setRendererName($rendererName): ViewInterface;

    /**
     * Nastaví template objekt pro renderování. Tato template bude použita metodou render().
     *
     * @param TemplateInterface $template
     * @return \Pes\View\ViewInterface
     */
    public function setTemplate(TemplateInterface $template): ViewInterface;

    /**
     * Lze nastavit data pro renderování. Tato data budou použita metodou render().
     *
     * @param type $data
     * @return ViewInterface
     */
    public function setData($data): ViewInterface;
}

