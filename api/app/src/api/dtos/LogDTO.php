<?php

namespace api\dtos;

use Exception;

class LogDTO
{
    public function __construct(
        public string $id,
        public string $created_at,
        public string $acteur_id,
        public string $action_type,
        public ?array $details
    ) {}

    /**
     * @throws Exception
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new Exception("La propriété '$property' n'existe pas.");
    }
}
