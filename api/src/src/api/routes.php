<?php
declare(strict_types=1);

use api\actions\AdminLogsAction;
use api\actions\ConnexionAction;
use api\actions\CreateTransactionAction;
use api\actions\DeleteUserAction;
use api\actions\EnregistrerAction;
use api\actions\TransactionByIdAction;
use api\actions\TransactionsAction;
use api\actions\TransactionsBetweenAction;
use api\actions\UserByIdAction;
use api\actions\UserSoldeAction;
use api\actions\UsersListAction;
use api\middlewares\AuthnSigninValidationMiddleware;
use api\middlewares\EnregistrerUtilisateurMiddleware;
use Slim\App;


return function( App $app): App {
    $app->get('/admin/logs', AdminLogsAction::class);
    $app->get('/admin/accounts', UsersListAction::class);
    $app->get('/admin/transactions', TransactionsAction::class);
    $app->get('/health', function ($request, $response) {
        $response->getBody()->write(json_encode(['status' => 'ok']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });
    $app->get('/users', UsersListAction::class);
    $app->get('/users/{id_user}', UserByIdAction::class);
    $app->get('/users/{id_user}/solde', UserSoldeAction::class);
    $app->delete('/users/{id_user}', DeleteUserAction::class);
    $app->get("/transactions/{id_emetteur}/{id_recepteur}", TransactionsBetweenAction::class);
    $app->get("/transactions/{id_user}", TransactionByIdAction::class);
    $app->get("/transactions", TransactionsAction::class);

//    FONCTIONNALITES ETENDUES
//    $app->get("/card/{id_card}", CarteByIdAction::class);
//    //récupérer les infos d’une carte (suite au scan d’un QR code notamment)
//    $app->get("/card/{id_card}/qrcode", GenerateQrCodeAction::class);
//    //génère l'image du QR Code contenant l'identifiant sécurisé de la carte
//    $app->post("/card",CreateCardAction::class);
//    //Activer une nouvelle carte (création en base)
//    $app->get("/invoice/{id_invoice}", GenerateInvoiceAction::class);


    $app->post("/transactions", CreateTransactionAction::class);
    $app->post('/auth/login', ConnexionAction::class)
        ->add(AuthnSigninValidationMiddleware::class);


    $app->post('/auth/register', EnregistrerAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());
    $app->post('/users', EnregistrerAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());

    return $app;
};