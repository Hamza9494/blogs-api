<?php
class HandleErrors
{
    public static function handleExceptions(Throwable $ex)
    {
        echo json_encode([
            "error message" => $ex->getMessage(),
            "error code" => $ex->getCode(),
            "error file" => $ex->getFile(),
            "error line" => $ex->getLine(),
        ]);
    }
}
