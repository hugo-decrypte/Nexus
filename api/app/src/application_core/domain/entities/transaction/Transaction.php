<?php

namespace application_core\domain\entities\transaction;

use Exception;

class Transaction
{
    private string $id;
    private string $created_at;
    private ?string $emetteur_id;
    private ?string $recepteur_id;
    private float $montant;
    private string $hash;
    private ?string $description;

    public function __construct(
        string $id,
        float $montant,
        string $hash,
        string $created_at,
        ?string $emetteur_id = null,
        ?string $recepteur_id = null,
        ?string $description = null
    ) {
        $this->id = $id;
        $this->montant = $montant;
        $this->hash = $hash;
        $this->created_at = $created_at;
        $this->emetteur_id = $emetteur_id;
        $this->recepteur_id = $recepteur_id;
        $this->description = $description;
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
