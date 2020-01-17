<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Validator;

/**
 * IsStringValidator ověřuje jestli parametr je string nebo přetypovatelný na string.
 *
 * @author pes2704
 */
class IsArrayKeyValidator implements ValidatorInterface {
    public function isValid($param) {
        $ret = (is_string($param) OR is_integer($param)) ? TRUE : FALSE;
        return $ret;
    }
}
