<?php
namespace api\dtos;

class InputUserDTO{
    private string $nom;
    private string $prenom;
    private string $email;
    private string $mot_de_passe;
    public function __construct(array $data){
        $this->nom = $data['nom'];
        $this->prenom = $data['prenom'];
        $this->email = $data['email'];
        $this->mot_de_passe = $data['mot_de_passe'];
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