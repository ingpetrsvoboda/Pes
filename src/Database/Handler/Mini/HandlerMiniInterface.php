<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Database\Handler\Mini;

/**
 *
 * @author pes2704
 */
interface HandlerMiniInterface extends PDOInterface {
    /**
     * @return DsnInterface
     */
    public function getDsn();}
