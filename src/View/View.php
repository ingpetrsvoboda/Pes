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
use Pes\View\Renderer\TemplateRendererInterface;
use Pes\View\Renderer\Container\TemplateRendererContainer;
use Pes\View\Template\TemplateInterface;

/**
 * use použit jako definice fallback rendereru - použit pro renderování, pokud nebyla zadána žádná šablona a tedy není znám default renderer šablony.
 */
use Pes\View\Renderer\ImplodeRenderer as FallbackRenderer;
use Pes\View\Template\ImplodeTemplate as FallbackTemplate;

/**
 * View - objekt je vytvořen se zadaným rendererem a s jeho použitím vytváří textový obsah z aktuálně zadaných dat.
 * Generovaný obsah je dán rendererem.
 *
 * @author pes2704
 */
class View implements ViewInterface {

    /**
     * @var RendererInterface
     */
    protected $renderer;

    protected $data;

    /**
     * @var TemplateInterface
     */
    protected $template;

    public function getRenderer(): RendererInterface {
        return $this->renderer;
    }

    /**
     * Nastaví renderer. Pokud jde o
     * @param RendererInterface $renderer
     * @return \Pes\View\ViewInterface
     */
    public function setRenderer(RendererInterface $renderer): ViewInterface {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Lze nastavit data pro renderování. Tato data budou použita metodou render().
     * @param type $data
     * @return ViewInterface
     */
    public function setData($data=NULL): ViewInterface {
        $this->data = $data;
        return $this;
    }

    /**
     * Nastaví template objekt pro renderování. Tato template bude použita metodou render().
     * @param TemplateInterface $template
     * @return \Pes\View\ViewInterface
     */
    public function setTemplate(TemplateInterface $template): ViewInterface {
        $this->template = $template;
        return $this;
    }

    /**
     * Renderuje data s použitím případné template a vytvoří obsah.
     *
     * Renderuje data:
     * <ol>
     * <li>Data zadaná jako parametr.</l>
     * <li>Pokud parameter data není zadán, renderuje data zadaná metodou setData (view->setData($data)).</li>
     * </ol>
     *
     * Použije renderer:
     * <ol>
     * <li>renderer nastavenž metodou View->setRenderer()</li>
     * <li>pokud je nastavena template, použije renderer z renderer kontejneru (RendererContainer) se jménem service získanou z template
     * metodou template->getDefaultRendererService()</li>
     * <li>fallback: pokud není zadán renderer, použije se fallback renderer pro získání alespoň nějakého výstupu. Jméno třídy fallback rendereru je definováno jako
     * alias FallbackRenderer v příkazu use uvedeném v třídě View.</li>
     * </ol>
     *
     * Použití:
     *
     * - Výchozí režim je pro renderování template je použití defaultního rendereru definovaného v template.
     * Defaultní template renderer lze přebít zadáním rendereru metodou setRenderer(). Od okažiku nastavení rendereru jsou všechny template renderovány zadaným rendererem.
     *
     * - Pro renderování bez template je samozřejmě nutné nastavit renderer metodou setRenderer() vždy.
     *
     * @param mixed $data
     * @return string
     */
    public function getString($data=NULL) {
        $this->resolveRenderer();
        if ($this->renderer instanceof TemplateRendererInterface) {
            return $this->renderer->render($this->template, $data ?? $this->data);
        } else {
            return $this->renderer->render($data ?? $this->data);
        }
    }

    private function resolveRenderer() {
        if (isset($this->renderer)) {
            if (isset($this->template)) {
                $templateDefaultRendererClass = $this->template->getDefaultRendererService();
                if ( !($this->renderer instanceof $templateDefaultRendererClass)) {
                    throw new \UnexpectedValueException("Zadaný renderer ". get_called_class($this->renderer)." není typem rendereru, který může renderovat template typu ".get_called_class($this->template).". Zadané template vyžaduje"
                            . " renderer typu $templateDefaultRendererClass.");
                }
            }
        } else {
            if ($this->template) {
                $this->createTemplateRenderer();
                if (!isset($this->renderer)) {
                    user_error("Nepodařilo se získat renderer z renderer kontejneru pro službu {$this->template->getDefaultRendererService()} vrácenou metodou getDefaultRendererService() zadané template ".get_called_class($this->template).". Použit fallback renderer a fallbach template.", E_USER_WARNING);
                }
            }
            if (!isset($this->renderer)) {
                user_error("Není nastaven renderer, použit fallback renderer a fallback template.", E_USER_NOTICE);
                $this->renderer = new FallbackRenderer();
                $this->template = new FallbackTemplate();
            }
        }
    }

    private function createTemplateRenderer() {
        $this->renderer = TemplateRendererContainer::get($this->template->getDefaultRendererService());
    }

    /**
     * Metoda umožňuje použít objekt view přímo jako proměnnou (proměnou v šabloně) pro další view.
     *
     * Interně volá metodu třídy View->getString(). Pokud je třeba renderovat nějaká data, je nutné je zadat metodou setData($data).
     *
     * Protože v PHP nesmí při vykonávání magické metodu __toString dojít k výjimc , volání getString je v try-cath bloku a případná výjimka
     * je převedena na E_USER_ERROR.
     * To obvykle vede na Fatal error, ale nezobrazí se zavádějící hlášení o výjimce v metodě __toString s řádkem chyby, kterým je řádek v templatě,
     * ve které došlo k pokusu o renderování nějakého view, který byl použit jako proměnná.
     *
     * Pokud je potřebné vyhazovat výjimky z důvodu jejich zachycování nebo pro ladění, je třeba volat přímo metodu View->getString().
     *
     * @return string
     */
    public function __toString() {
        //

            try {
                $str = $this->getString();
            } catch (\Throwable $e) {
                user_error(' Výjimka pri vykonávání metody __toString: '.$e->getMessage().' in: '.$e->getFile().' on line: '.$e->getLine().'.', E_USER_ERROR);
                $str = '';
            }

        return $str;
    }

}