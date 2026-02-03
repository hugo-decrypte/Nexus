<?php

namespace api\dtos;

class CredentialsDTO{

    private string $nom;
    private string $prenom;
    private string $email;
    private string $mot_de_passe;

    public function __construct(string $nom, string $prenom, string $email, string $mot_de_passe)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
    }

    public function __get(string $name){
        if(property_exists($this,$name)) {
            return $this->$name;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }
}