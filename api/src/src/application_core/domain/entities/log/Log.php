<?php

namespace application_core\domain\entities\log;

use Exception;

class Log
{
    private string $id;
    private string $created_at;
    private ?int $acteur_id;
    private string $action_type;
    private ?array $details;

    public function __construct(
        string $id,
        string $action_type,
        ?int $acteur_id = null,
        ?array $details = null
    ) {
        $this->id = $id;
        $this->action_type = $action_type;
        $this->created_at = date('Y-m-d H:i:s');
        $this->acteur_id = $acteur_id;
        $this->details = $details;
    }

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
