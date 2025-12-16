<?php

namespace api\dtos;

use Exception;

class InputAuthnDTO {
    private string $email;
    private string $mot_de_passe;

    public function __construct(array $data) {
        $this->email = $data['email'];
        $this->mot_de_passe = $data['mot_de_passe'];
    }

    /**
     * @throws Exception
     */
    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new Exception("La propriété '$property' n'existe pas.");
    }
}
