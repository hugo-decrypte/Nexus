<?php
namespace api\dtos;

class TransactionDTO{
    private string $id;
    private ?int $emetteur_id;
    private ?int $recepteur_id;
    private float $montant;
    private string $hash;
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->montant = $data['montant'];
        $this->hash = $data['hash'];
        $this->emetteur_id = $data['emetteur_id'];
        $this->recepteur_id = $data['recepteur_id'];
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