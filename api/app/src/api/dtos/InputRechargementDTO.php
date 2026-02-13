<?php
namespace api\dtos;

class InputRechargementDTO{
    private string $id_recepteur;
    private string $montant;
    public function __construct(array $data){
        $this->id_recepteur = $data['id_recepteur'];
        $this->montant = $data['montant'];
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