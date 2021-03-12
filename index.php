<?php

require_once 'vendor/autoload.php';

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 'on');
}

ini_set('session.cookie_samesite', 'Lax');

use ERTNoteMaker\Database;
use ERTNoteMaker\Model\Raid;
use ERTNoteMaker\Model\RaidEncounter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Session\Session;

$adminKey = file_get_contents('config/admin_key.txt');
$session  = new Session();
$session->setName('enm-session-id');
$loader   = new \Twig\Loader\FilesystemLoader('views');
$twig     = new \Twig\Environment($loader, [
    'debug' => true
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());
$twig->addGlobal('flashes', $session->getFlashBag()->all());

$app = AppFactory::create();
$database = Database::create(true);
$raidRepo = $database->getRepository('ERTNoteMaker\Model\Raid');
$encounterRepo = $database->getRepository('ERTNoteMaker\Model\RaidEncounter');

$app->get('/', function (Request $request, Response $response, array $args) use ($twig, $encounterRepo) {
    $encounters = $encounterRepo->findAll();

    $response->getBody()->write($twig->render('index.html.twig', [
        'encounters' => $encounters
    ]));
    return $response;
});

/* ============================================================================= */
/* ============================================================================= */
/* ============================================================================= */
/* =================================== ADMIN =================================== */
/* ============================================================================= */
/* ============================================================================= */
/* ============================================================================= */

$requestBag = new ParameterBag($_REQUEST);

if ($requestBag->get('key') === $adminKey || $session->get('key') === $adminKey) {
    $session->set('key', $adminKey);

    $app->get('/admin/add-raid', function (Request $request, Response $response, array $args) use ($twig) {
        $response->getBody()->write($twig->render('admin/add-raid.html.twig'));
        return $response;
    });

    $app->post('/admin/add-raid',function (Request $request, Response $response, array $args) use ($session) {
        $data = $request->getParsedBody();

        $raid = new Raid($data['name']);
        $raid->save();

        $session->getFlashBag()->add('success', "Raid added!");

        return $response
            ->withHeader("Location", "/admin/add-raid")
            ->withStatus(302);
    });

    $app->get('/admin/add-encounter', function (Request $request, Response $response, array $args) use ($twig, $raidRepo) {
        $raids = $raidRepo->findAll();

        $response->getBody()->write($twig->render('admin/add-encounter.html.twig', [
            'raids' => $raids
        ]));
        return $response;
    });

    $app->post('/admin/add-encounter',function (Request $request, Response $response, array $args) use ($session, $raidRepo) {
        $data = $request->getParsedBody();

        $raid = $raidRepo->find($data['raid_id']);
        if (empty($raid)) {
            $session->getFlashBag()->add('error', "Invalid raid selected.");
            return $response
                ->withHeader("Location", "/admin/add-encounter")
                ->withStatus(302);
        }

        $encounter = new RaidEncounter($raid, $data['name'], $data['encounter_id']);
        $encounter->save();

        $session->getFlashBag()->add('success', "Raid encounter added!");

        return $response
            ->withHeader("Location", "/admin/add-encounter")
            ->withStatus(302);
    });
}

$app->run();