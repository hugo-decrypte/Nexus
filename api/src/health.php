<?php
// Fichier de healthcheck simple
http_response_code(200);
echo json_encode(['status' => 'ok']);
