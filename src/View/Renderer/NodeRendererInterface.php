<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer;

/**
 *
 * @author pes2704
 */
interface NodeRendererInterface {
    /**
     * Přijímá separator, sodnotu. která bude vložena mezi jendotlivé vyrenderované tagy.
     */
    public function setSeparator($separator);

    /**
     * Data se nijak nezpracovávají!!
     * @param type $data
     * @return string
     */
    public function render(NodeTemplateInterface $template, $data=NULL);
}