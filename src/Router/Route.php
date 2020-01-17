<?php

namespace Pes\Router;

/**
 * Description of Route
 *
 * @author pes2704
 */
class Route implements RouteInterface {

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $urlPattern;

    /**
     * @var string
     */
    private $patternPreg;

    /**
     * @var string
     */
    private $action;

    public function getMethod() {
        return $this->method;
    }

    /**
     *
     * @return string Vrací zadaný urlPattern
     */
    public function getUrlPattern() {
        return $this->urlPattern;
    }

    /**
     *
     * @return string Vrací regulární výraz vytvořený z parametru urlPattern
     */
    public function getPatternPreg() {
        return $this->patternPreg;
    }

    /**
     * @return callable Vrací spustitelnou akci routy.
     * @return callable
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Vrací REST path vytvořenou s použitím pattern routy a zadaných parametrů path. Parametry jsou vloženy na místa proměnných v pattern.
     *
     * @param array $pathParams
     * @return string
     * @throws UnexpectedValueException
     */
    public function getPathFor(array $pathParams) {
        $replaced = 0;
        $pattern = $this->urlPattern;
        foreach ($pathParams as $key => $value) {
            $pattern = str_replace(':'.$key, $value, $pattern, $replaced);
            if ($replaced==0) {
                throw new UnexpectedValueException("Nenalezen parametr v pattern routy. Parametr: '$key'.");
            } elseif ($replaced>1) {
                throw new UnexpectedValueException("Nalezaeno více stejně pojmenovaných parametrů v pattern routy. Parametr: '$key'.");
            }
        }
        return $this->filterPath($pattern);
    }

    /**
     * Filter Uri path.
     *
     * This method percent-encodes all reserved
     * characters in the provided path string. This method
     * will NOT double-encode characters that are already
     * percent-encoded.
     *
     * @param  string $path The raw uri path.
     * @return string       The RFC 3986 percent-encoded uri path.
     * @link   http://www.faqs.org/rfcs/rfc3986.html
     */
    private function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    /**
     * Přijímá hodnoty výčtového typu MethodEnum. V případě neexistující hodnoty vyhodí objekt MethodEnum svoji výjimku.
     *
     * @param string $method Existující hodnota výčtového typu MethodEnum.
     * @return \Pes\Router\RouteInterface
     */
    public function setMethod($method): RouteInterface {
        $this->method = (new MethodEnum())($method);
        return $this;
    }

    /**
     * Nastaví pattern routy. Kontroluje přípustný formát pattern a v případě chybného formátu vyhodí výjimnku.
     * Pattern routy začíná i končí znakem '/' a může obsahovat segmenty oddělené znakem '/'. Pattern, který nemá segmenty je '/'.
     * Jednotlivé segmenty jsou dvojího druhu:
     *
     * @param string $urlPattern
     * @return \Pes\Router\RouteInterface
     * @throws \UnexpectedValueException Chybný formát pattern...
     */
    public function setUrlPattern($urlPattern): RouteInterface {
        if ($urlPattern == '') {
            throw new \UnexpectedValueException("Chybný formát pattern. Pattern routy nesmí být prázdný řetězec.");
        }
        if ($urlPattern[0] != '/') {
            throw new \UnexpectedValueException("Chybný formát pattern. Pattern routy musí začínat znakem '/'. Zadán pattern: $urlPattern");
        }
//        if ($urlPattern[-1] != '/') {
//            throw new \UnexpectedValueException("Chybný formát pattern. Pattern routy musí končit znakem '/'. Zadán pattern: $urlPattern");
//        }
        if (($urlPattern[1] ?? '') == ':') {
            throw new \UnexpectedValueException("Chybný formát pattern. Pattern routy nesmí na první pozici zleva obsahovat parametr. Zadán pattern: $urlPattern");
        }
        $this->urlPattern = $urlPattern;
        // konvertuje route url na regulární výraz - obalí pattern routy znaky začátku a konce regulárního výrazu
        // a nahradí části začínající : výrazem ([a-zA-Z0-9\-\_]+)
        // Příklad: url "/node/:id/add/" kovertuje na regulární výraz "@^/node/([a-zA-Z0-9\-\_]+)/add/$@D"
        // když není nastaveno /u -> neumí utf8 jen ascii a tedy neumí písmenka s diakritikou
        $this->patternPreg = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/u', '([a-zA-Z0-9\-\_]+)', preg_quote($this->urlPattern)) . "$@D";
        return $this;
    }

    /**
     *
     * @param callable $action
     * @return \Pes\Router\RouteInterface
     */
    public function setAction(callable $action): RouteInterface {
        $this->action = $action;
        return $this;
    }


}
