<?php

namespace Pes\View;

use Pes\View\Template\TemplateInterface;
use Pes\View\Renderer\TemplateRendererInterface;
use Pes\View\Renderer\RecordableRendererInterface;

use Pes\View\Renderer\RendererContainer;
use Pes\View\Renderer\ImplodeRenderer;
/**
 *
 * @author pes2704
 */
class CompositeView extends View implements CompositeViewInterface {

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     *
     * @var ViewInterface in \SplObjectStorage
     */
    private $componentViews;

    /**
     * Přijímá kompozitní renderer, renderer používaný pro renderování kompozice (nadřazený, layout renderer).
     *
     * @param RendererInterface $renderer
     */
    public function setRenderer(Renderer\RendererInterface $renderer): ViewInterface {
        $this->renderer = $renderer;
        $this->componentViews = new \SplObjectStorage();
        return $this;
    }

    /**
     * Metoda pro přidání komponentních view. Jednotlivá komponentní view budou renderována a vygenerovaný výsledek bude vložen
     * do kompozitního view na místo proměnné zadané zde jako jméno.
     * Jednotlivá komponentní view budou renderována bez předání (nastavení) template a dat, musí mít tedy před renderováním kompozitního view nastavenu šablonu
     * a data pokud je potřebují pro své renderování.
     *
     * @param ViewInterface $componentView Komponetní view
     * @param string $name Jméno proměnné v kompozitním view, která má být nahrazena výstupem zadané komponentní view
     */
    public function appendComponentView(ViewInterface $componentView, $name) {
        $this->componentViews->attach($componentView, $name);
    }

    /**
     * Metoda renderuje všechny vložené component renderery. Výstupní kód z jednotlivých renderování vkládá do kontextu
     * composer rendereru vždy pod jménem proměnné, se kterým byl component renderer přidán. Nakonec renderuje
     * compose renderer. Při renderování compose rendereru použije data zadaná jako parametr, pokud nebyla zadána, data zadaná metodou setData($data).
     *
     * @param mixed $data
     * @return string
     */
    public function getString($data=NULL) {

        $composeViewData = array();
        if (count($this->componentViews)>0) {
            foreach ($this->componentViews as $componentView) {
                /* @var ViewInterface $componentView */
                if (isset($this->recorderProvider)) {
                    $componentView->setRecorderProvider($this->recorderProvider);
                }
                $composeViewData[$this->componentViews->getInfo()] = $componentView->getString();
            }
        }
        // $composeViewData se musí spojit se správnými daty už tady. Buď s $data, pokud byla zadána nebo $this->data.
        if($this->data) {
            $data = array_merge($data ?? $this->data, $composeViewData);
        } else {
            $data = $composeViewData;
        }
        return parent::render($data);
    }
}
