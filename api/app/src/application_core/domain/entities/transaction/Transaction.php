<?php

namespace application_core\domain\entities\transaction;

use Exception;

class Transaction
{
    private string $id;
    private string $created_at;
    private ?int $emetteur_id;
    private ?int $recepteur_id;
    private float $montant;
    private string $hash;

    public function __construct(
        string $id,
        float $montant,
        string $hash,
        ?int $emetteur_id = null,
        ?int $recepteur_id = null
    ) {
        $this->id = $id;
        $this->montant = $montant;
        $this->hash = $hash;
        $this->created_at = date('Y-m-d H:i:s');
        $this->emetteur_id = $emetteur_id;
        $this->recepteur_id = $recepteur_id;
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
