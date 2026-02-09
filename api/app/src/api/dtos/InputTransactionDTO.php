<?php
namespace api\dtos;

class InputTransactionDTO{
    private string $id_emetteur;
    private string $id_recepteur;
    private string $montant;
    private ?string $description;
    public function __construct(array $data){
        $this->id_emetteur = $data['id_emetteur'];
        $this->id_recepteur = $data['id_recepteur'];
        $this->montant = $data['montant'];
        $this->description = $data['description'] ?? null;
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