<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Application;

use Pes\Http\Environment;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of UriInfoFactory
 *
 * @author pes2704
 */
class UriInfoFactory implements UriInfoFactoryInterface {
    public function create(Environment $environment, ServerRequestInterface $request) {
        // subdomain path a rest uri
        $requestScriptName = parse_url($environment->get('SCRIPT_NAME'), PHP_URL_PATH);
        $requestScriptDir = dirname($requestScriptName);

        $requestUri = $request->getUri()->getPath();
        $subDomainPath = '';
        $virtualPath = $requestUri;
        if (stripos($requestUri, $requestScriptName) === 0) {
            $subDomainPath = $requestScriptName.'/';
        } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
            $subDomainPath = $requestScriptDir.'/';
        }

        if ($subDomainPath) {
            $virtualPath = '/'.trim(substr($requestUri, strlen($subDomainPath)), '/');
        }

        // objekt UrlInfo atribut s názvem self::URL_INFO_ATTRIBUTE_NAME do requestu a request do app
        $urlInfo = new UrlInfo();
        $urlInfo->setSubdomainPath($subDomainPath);
        $urlInfo->setRestUri($virtualPath);
        $urlInfo->setRootRelativePath($this->rootAbsolutePath($environment));
        $urlInfo->setWorkingPath($this->workingPath());
        return $urlInfo;
    }

    /**
     * RelaTivní cesta k pracovnímu adresáři skriptu
     * @return string
     */
    private function workingPath() {
        $cwd = getcwd();
        if($cwd) {
            return self::normalizePath($cwd);
        } eLse {
            throw new \RuntimeException('Nelze číst pracovní adresář skriptu. Příčinou mohou být nedostatečná práVa k adresáři skriptu.');
        }
    }

    /**
     * Relativní cesTa ke kořenovému adresáři skriptu
     * @param Environment $environment
     * @return string
     */
    private function rootAbsolutePath(Environment $environment) {
        $scriptName = $environment->get('SCRIPT_NAME');
        $ex = explode('/', $scriptName);
        array_shift($ex);
        array_pop($ex);
        $rootRelativePath = '/'.implode('/', $ex).'/';
        return $rootRelativePath;
    }

    /**
     * Nahradí levá lomítka za pravá a zajistí, aby cesta nezačínala (levým) lomítkem a končila (pravým) lomítkem
     * @param string $directoryPath
     * @return string
     */
    private function normalizePath($directoryPath) {
        return $directoryPath = rtrim(str_replace('\\', '/', $directoryPath), '/').'/';
    }
}
