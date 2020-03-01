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

use Pes\View\Recorder\RecorderProviderInterface;

use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\Template\FileTemplateAbstract;
use Pes\View\Renderer\NodeRenderer;
use Pes\View\Template\NodeTemplate;
use Pes\Dom\Node\NodeInterface;

/**
 * Description of ViewFactory
 *
 * Vytvoří nový view.
 *
 * @author pes2704
 */
class ViewFactory {

    /**
     * Vytvoří nový view, přímo přetypovatelný na text. Pokud jsou zadána data, nastaví tomuto view i data, to je třeba, pokud zadaná šablona obsahuje proměnné.
     * Vytvořený objekt view je vhodný jako proměnná do šablony nebo jako view pro node typu TextView.
     *
     * Podrobně:
     * Vytvoří nový objekt view, nastaví mu nově vytvořený renderer a template.
     * Typy view, rendereru a template jsou dány deklaracemi use uvedenými ve třídě ViewFactory. Metoda vytvořenému view nastaví
     * data potřebná pro renderování a případně i záznamový objekt pro záznam o užití dat při renderování.
     * Výsledný view obsahuje vše potřebné pro renderování a lze ho kdykoli přetypovat na text.
     *
     * @param type $templateFilename
     * @param type $data
     * @param RecordLoggerInterface $recorderProvider <p>Nastaví objekt pro logování informací o užití
     *      proměnných v šabloně. Pokud je nastaven $recorderProvider a zde vytvářený template renderer je typu RecordableRendererInterface
     *      poskytne tento RocordLogger rekorder a renderer zaznamená užití dat při renderování šablon.</p>
     * @return View
     */
    public static function viewWithPhpTemplate($templateFilename, $data=[], RecorderProviderInterface $recorderProvider=NULL): View {

        $template = (new FileTemplateAbstract($templateFilename));
        $renderer = new PhpTemplateRenderer($template);
        if ($recorderProvider) {
            $renderer->setRecorderProvider($recorderProvider);
        }
        $view = (new View())->setRenderer($renderer);
        if ($data) {
            $view->setData($data);
        }
        return $view;
    }

    /**
     * Vytvoří nový view a nastaví mu rendererer a zadaný tag.
     * @param TagInterface $node
     * @return View
     */
    public static function viewWithNode(NodeInterface $node, RecorderProviderInterface $recorderProvider=NULL): View {
        $template = new NodeTemplate($node);
        $renderer = new NodeRenderer($template);
        if ($recorderProvider) {
            $renderer->setRecorderProvider($recorderProvider);
        }
        $view = (new View())->setRenderer($renderer);
        return $view;
    }
}
