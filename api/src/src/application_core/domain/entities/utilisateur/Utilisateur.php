<?php

namespace application_core\domain\entities\utilisateur;

use Exception;

class Utilisateur
{
    private string $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $mot_de_passe;
    private string $role;
    private string $date_creation;

    public function __construct(
        string $id,
        string $nom,
        string $prenom,
        string $email,
        string $mot_de_passe,
        string $role
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->role = $role;
        $this->date_creation = date('Y-m-d H:i:s');
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
