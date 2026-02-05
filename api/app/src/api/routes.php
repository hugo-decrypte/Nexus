<?php
declare(strict_types=1);

use api\actions\AdminLogsAction;
use api\actions\SigninAction;
use api\actions\CreateTransactionAction;
use api\actions\DeleteUserAction;
use api\actions\RegisterAction;
use api\actions\TransactionByIdAction;
use api\actions\TransactionsAction;
use api\actions\TransactionsBetweenAction;
use api\actions\UserByIdAction;
use api\actions\UsersListAction;
use api\actions\UserSoldeAction;
use api\middlewares\AuthnSigninValidationMiddleware;
use api\middlewares\authz\AuthzAdminMiddleware;
use api\middlewares\authz\AuthzClientMiddleware;
use api\middlewares\authz\AuthzCommercantMiddleware;
use api\middlewares\authz\AuthzCreateTransactionMiddleware;
use api\middlewares\authz\AuthzGetTransactionMiddleware;
use api\middlewares\authz\AuthzUserMiddleware;
use api\middlewares\authz\AuthzUserRessourceAccessMiddleware;
use api\middlewares\CreateTransactionMiddleware;
use api\middlewares\EnregistrerUtilisateurMiddleware;
use api\middlewares\JwtAuthMiddleware;
use Slim\App;


return function( App $app): App {
    $app->get('/admin/logs', AdminLogsAction::class)
        ->add(AuthzAdminMiddleware::class)
        ->add(JwtAuthMiddleware::class);
    $app->get('/admin/accounts', UsersListAction::class)
        ->add(AuthzAdminMiddleware::class)
        ->add(JwtAuthMiddleware::class);
    $app->get('/admin/transactions', TransactionsAction::class)
        ->add(AuthzAdminMiddleware::class)
        ->add(JwtAuthMiddleware::class);
    $app->get('/health', function ($request, $response) {
        $response->getBody()->write(json_encode(['status' => 'ok']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });
    $app->get('/users', UsersListAction::class)
        ->add(AuthzAdminMiddleware::class)
        ->add(JwtAuthMiddleware::class);
    $app->get('/users/{id_user}', UserByIdAction::class)
        ->add(new AuthzUserRessourceAccessMiddleware())
        ->add(JwtAuthMiddleware::class);

    $app->get('/users/{id_user}/solde', UserSoldeAction::class)
        ->add(new AuthzUserRessourceAccessMiddleware())
        ->add(JwtAuthMiddleware::class);

    $app->delete('/users/{id_user}', DeleteUserAction::class)
        ->add(AuthzAdminMiddleware::class)
        ->add(JwtAuthMiddleware::class);

    $app->get("/transactions/{id_emetteur}/{id_recepteur}", TransactionsBetweenAction::class)
        ->add(new AuthzGetTransactionMiddleware())
        ->add(JwtAuthMiddleware::class);
    $app->get("/transactions/{id_user}", TransactionByIdAction::class)
        ->add(new AuthzUserRessourceAccessMiddleware())
        ->add(JwtAuthMiddleware::class);
    $app->get("/transactions", TransactionsAction::class)
        ->add(AuthzAdminMiddleware::class)
        ->add(JwtAuthMiddleware::class);

//    FONCTIONNALITES ETENDUES
//    $app->get("/card/{id_card}", CarteByIdAction::class);
//    //récupérer les infos d’une carte (suite au scan d’un QR code notamment)
//    $app->get("/card/{id_card}/qrcode", GenerateQrCodeAction::class);
//    //génère l'image du QR Code contenant l'identifiant sécurisé de la carte
//    $app->post("/card",CreateCardAction::class);
//    //Activer une nouvelle carte (création en base)
//    $app->get("/invoice/{id_invoice}", GenerateInvoiceAction::class);


    $app->post("/transactions", CreateTransactionAction::class)
        ->add(new AuthzCreateTransactionMiddleware())
        ->add(new CreateTransactionMiddleware())
        ->add(JwtAuthMiddleware::class);;
    $app->post('/auth/login', SigninAction::class)
        ->add(AuthnSigninValidationMiddleware::class)
        ->add(JwtAuthMiddleware::class);


    $app->post('/auth/register', RegisterAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());
    $app->post('/users', RegisterAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());

    return $app;
};