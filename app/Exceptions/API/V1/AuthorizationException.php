<?php

namespace App\Exceptions\API\V1;

use App\Traits\Response;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthorizationException extends Exception
{
    use Response;

    public function report()
    {
        Log::info($this->getMessage() . " ======> ", [
            "file:" => $this->getFile(),
            "line:" => $this->getLine(),
            "status_code:" => $this->getCode(),
        ]);
    }

    public function render($request)
    {
        return $this->errorResponse(__("messages.something_went_wrong"), [
            "message" => $this->getMessage(),
            "file" => $this->getFile(),
            "line" => $this->getLine(),
        ], $this->getCode());
    }
}
