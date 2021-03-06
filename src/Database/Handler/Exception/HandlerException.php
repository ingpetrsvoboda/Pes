<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Handler\Exception;

/**
 * Description of HandlerException
 *
 * @author pes2704
 */
class HandlerException extends \PDOException implements HandlerExceptionInterface {
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = NULL) {
        parent::__construct($message.PHP_EOL.$previous->getTraceAsString(), $code, $previous);
    }
}