<?php
namespace api\dtos;

class TransactionDTO{

    public function __construct(
        public string $id,
        public float $montant,
        public string $hash,
        public ?string $emetteur_id = null,
        public ?string $recepteur_id = null,
        public ?string $description = null,
        public ?string $created_at = null,
    ) {
        if ($this->montant <= 0) {
            throw new \InvalidArgumentException("Le montant doit être supérieur à zéro.");
        }
    }

    public function __get(string $name){
        if(property_exists($this,$name)) {
            return $this->$name;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }

    public function __set(string $name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
            return;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }
}