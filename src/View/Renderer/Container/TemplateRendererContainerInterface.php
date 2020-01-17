<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer\Container;

use Pes\View\Recorder\RecorderProviderInterface;
use Pes\View\Template\TemplateInterface;
use Pes\View\Renderer\RendererInterface;

/**
 *
 * @author pes2704
 */
interface TemplateRendererContainerInterface {
    public static function get(TemplateInterface $template);
    public static function setRecorderProvider(RecorderProviderInterface $recorderProvider);
}
