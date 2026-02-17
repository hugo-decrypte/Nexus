<?php

namespace api\dtos;

class GoogleLoginDTO
{

    private string $google_id;

    public function __construct(string $google_id)
    {
        $this->google_id = $google_id;
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }
}