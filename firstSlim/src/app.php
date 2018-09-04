<?php
namespace feather\firstSlim;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require './vendor/autoload.php';

class App
{

   private $app;
   public function __construct($db) {

     $config['db']['host']   = 'localhost';
     $config['db']['user']   = 'root';
     $config['db']['pass']   = 'root';
     $config['db']['dbname'] = 'apidb';

     $app = new \Slim\App(['settings' => $config]);

     $container = $app->getContainer();
     $container['db'] = $db;

     $app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
         $name = $args['name'];
         $response->getBody()->write("Hello, $name");

         return $response;
     });
     $app->get('/Athletes', function (Request $request, Response $response) {
         $Athletes = $this->db->query('SELECT * from Athletes')->fetchAll();
         $jsonResponse = $response->withJson($Athletes);
         return $jsonResponse;
     });
     $app->get('/Athletes/{id}', function (Request $request, Response $response, array $args) {
         $id = $args['id'];
         $Athlete = $this->db->query('SELECT * from Athletes where id='.$id)->fetch();

         if($Athlete){
           $response =  $response->withJson($Athlete);
         } else {
           $errorData = array('status' => 404, 'message' => 'not found');
           $response = $response->withJson($errorData, 404);
         }
         return $response;

     });
     $app->put('/Athletes/{id}', function (Request $request, Response $response, array $args) {
         $id = $args['id'];

         // check that peron exists
         $Athlete = $this->db->query('SELECT * from Athletes where id='.$id)->fetch();
         if(!$Athlete){
           $errorData = array('status' => 404, 'message' => 'not found');
           $response = $response->withJson($errorData, 404);
           return $response;
         }

         // build query string
         $updateString = "UPDATE Athletes SET ";
         $fields = $request->getParsedBody();
         $keysArray = array_keys($fields);
         $last_key = end($keysArray);
         foreach($fields as $field => $value) {
           $updateString = $updateString . "$field = '$value'";
           if ($field != $last_key) {
             // conditionally add a comma to avoid sql syntax problems
             $updateString = $updateString . ", ";
           }
         }
         $updateString = $updateString . " WHERE id = $id;";

         // execute query
         try {
           $this->db->exec($updateString);
         } catch (\PDOException $e) {
           $errorData = array('status' => 400, 'message' => 'Invalid data provided to update');
           return $response->withJson($errorData, 400);
         }
         // return updated record
         $Athlete = $this->db->query('SELECT * from Athletes where id='.$id)->fetch();
         $jsonResponse = $response->withJson($Athlete);

         return $jsonResponse;
     });
     $app->delete('/Athletes/{id}', function (Request $request, Response $response, array $args) {
       $id = $args['id'];
       $deleteSuccessful = $this->db->exec('DELETE FROM Athletes where id='.$id);
       if($deleteSuccessful){
         $response = $response->withStatus(200);
       } else {
         $errorData = array('status' => 404, 'message' => 'not found');
         $response = $response->withJson($errorData, 404);
       }
       return $response;
     });

     $this->app = $app;
   }

   /**
    * Get an instance of the application.
    *
    * @return \Slim\App
    */
   public function get()
   {
       return $this->app;
   }
 }
