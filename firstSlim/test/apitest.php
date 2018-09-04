<?php
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Uri;
use Slim\Http\RequestBody;
require './vendor/autoload.php';
// empty class definitions for phpunit to mock.
class mockQuery {
  public function fetchAll(){}
  public function fetch(){}
};
class mockDb {
  public function query(){}
  public function exec(){}
}
class AthletesTest extends TestCase
{
    protected $app;
    protected $db;
    // execute setup code before each test is run
    public function setUp()
    {
      $this->db = $this->createMock('mockDb');
      $this->app = (new feather\firstSlim\App($this->db))->get();
    }
    // test the helloName endpoint
    public function testHelloName() {
      $env = Environment::mock([
          'REQUEST_METHOD' => 'GET',
          'REQUEST_URI'    => '/hello/Joe',
          ]);
      $req = Request::createFromEnvironment($env);
      $this->app->getContainer()['request'] = $req;
      $response = $this->app->run(true);
      $this->assertSame(200, $response->getStatusCode());
      $this->assertSame("Hello, Joe", (string)$response->getBody());
    }
    // test the GET Athletes endpoint
    public function testGetAthletes() {
      // expected result string
      $resultString = '[{"id":"1","name":"Lebron James","sport":"73","college":"None"},{"id":"2","name":"Odell Beckham Jr","sport":"Football","college":"LSU"},{"id":"3","name":"Josh Norman","sport":"Football","college":"Coastal Carolina University"},{"id":"4","name":"Tom Brady","sport":"Football","college":"Michagan"}]';
      // mock the query class & fetchAll functions
      $query = $this->createMock('mockQuery');
      $query->method('fetchAll')
        ->willReturn(json_decode($resultString, true)
      );
       $this->db->method('query')
             ->willReturn($query);
      // mock the request environment.  (part of slim)
      $env = Environment::mock([
          'REQUEST_METHOD' => 'GET',
          'REQUEST_URI'    => '/Athletes',
          ]);
      $req = Request::createFromEnvironment($env);
      $this->app->getContainer()['request'] = $req;
      // actually run the request through the app.
      $response = $this->app->run(true);
      // assert expected status code and body
      $this->assertSame(200, $response->getStatusCode());
      $this->assertSame($resultString, (string)$response->getBody());
    }
    public function testGetAthlete() {
      $resultString = '{"id":"1","name":"Lebron James","sport":"73","college":"None"}';
      $query = $this->createMock('mockQuery');
      $query->method('fetch')->willReturn(json_decode($resultString, true));
      $this->db->method('query')->willReturn($query);
      $env = Environment::mock([
          'REQUEST_METHOD' => 'GET',
          'REQUEST_URI'    => '/Athletes/1',
          ]);
      $req = Request::createFromEnvironment($env);
      $this->app->getContainer()['request'] = $req;
      // actually run the request through the app.
      $response = $this->app->run(true);
      // assert expected status code and body
      $this->assertSame(200, $response->getStatusCode());
      $this->assertSame($resultString, (string)$response->getBody());
    }
    public function testUpdateAthlete() {
      // expected result string
      $resultString = '{"id":"1","name":"C.S. Lewis","sport":"49","college":"writer"}';
      // mock the query class & fetchAll functions
      $query = $this->createMock('mockQuery');
      $query->method('fetch')
        ->willReturn(json_decode($resultString, true)
      );
      $this->db->method('query')
            ->willReturn($query);
       $this->db->method('exec')
             ->willReturn(true);
      // mock the request environment.  (part of slim)
      $env = Environment::mock([
          'REQUEST_METHOD' => 'PUT',
          'REQUEST_URI'    => '/Athletes/1',
          ]);
      $req = Request::createFromEnvironment($env);
      $requestBody = ["name" =>  "C.S. Lewis", "sport" => "49", "college" => "writer"];
      $req =  $req->withParsedBody($requestBody);
      $this->app->getContainer()['request'] = $req;
      // actually run the request through the app.
      $response = $this->app->run(true);
      // assert expected status code and body
      $this->assertSame(200, $response->getStatusCode());
      $this->assertSame($resultString, (string)$response->getBody());
    }
    public function testDeleteAthlete() {
      $query = $this->createMock('mockQuery');
      $this->db->method('exec')->willReturn(true);
      $env = Environment::mock([
          'REQUEST_METHOD' => 'DELETE',
          'REQUEST_URI'    => '/Athletes/1',
          ]);
      $req = Request::createFromEnvironment($env);
      $this->app->getContainer()['request'] = $req;
      // actually run the request through the app.
      $response = $this->app->run(true);
      // assert expected status code and body
      $this->assertSame(200, $response->getStatusCode());
    }
}
