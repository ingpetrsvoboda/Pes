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
    public function setRenderer(RendererInterface $renderer): ViewInterface;
    public function setRendererContainer(ContainerInterface $rendererContainer): ViewInterface;
    public function setRendererName($rendererName): ViewInterface;
    public function setData($data): ViewInterface;
}

