<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Template;

use Pes\Dom\Node\NodeInterface;

/**
 *
 * @author pes2704
 */
interface NodeTemplateInterface extends TemplateInterface {
//    public function setNode(NodeInterface $node): NodeTemplateInterface;
    public function getNode(): NodeInterface;

}
