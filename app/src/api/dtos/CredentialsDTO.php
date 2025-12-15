<?php

namespace nexus\api\dtos;

class CredentialsDTO{

    private string $email;
    private string $mot_de_passe;

    public function __construct(string $email, string $mot_de_passe)
    {
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
    }

    public function __get(string $name){
        if(property_exists($this,$name)) {
            return $this->$name;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }
}