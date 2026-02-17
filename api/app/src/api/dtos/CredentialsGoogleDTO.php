<?php

namespace api\dtos;

class CredentialsGoogleDTO{

    private string $nom;
    private string $prenom;
    private string $email;
    private string $google_id;

    public function __construct(string $nom, string $prenom, string $email, string $google_id)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->google_id = $google_id;
    }

    public function __get(string $name){
        if(property_exists($this,$name)) {
            return $this->$name;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }
}