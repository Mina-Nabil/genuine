<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait AlertFrontEnd
{
    public function alertSuccess($message)
    {
        $this->dispatch(
            'toastalert',
            detail: [
                'message' => $message,
                'type' => 'success',
            ],
        );
    }

    public function alertFailed($message = 'Server error')
    {
        // Default message for failed
        $this->dispatch(
            'toastalert',
            detail: [
                'message' => $message, // Use the default if no message provided
                'type' => 'failed',
            ],
        );
    }

    public function alertInfo($message)
    {
        $this->dispatch(
            'toastalert',
            detail: [
                'message' => $message,
                'type' => 'info',
            ],
        );
    }

    public function throwError($property, $message)
    {
        throw ValidationException::withMessages([
            $property => $message,
        ]);
    }
}
