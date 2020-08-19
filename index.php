<?php

require 'vendor/autoload.php';
	
$app = new \Slim\Slim();
$response = $app->response();

$app->get('/',function() use ($app,$response){
	echo "Welcome in API";
	
});

// API get user
$app->get('/user', function () use ($app, $response) {
	$db = DBConnection();
	$result = $db->query("select * from cs_user")->fetchAll();
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API show laporan penjualan
$app->get('/pemesanan', function () use ($app, $response) {
	$db = DBConnection();
	$result = $db->query("select * from cs_pemesanan")->fetchAll();
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API filter laporan penjualan
$app->get('/pemesanan/search/:keyword', function ($keyword) use ($app, $response) {
	$db = DBConnection();
	$result = $db->query("select * from cs_pemesanan WHERE orderat LIKE '%$keyword%'")->fetchAll();
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API get user by id
$app->get('/user/:id', function ($id) use ($app, $response) {
	$db = DBConnection();
	$result = $db->query("select * from cs_user where id=$id")->fetchAll();
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API search user
$app->get('/user/search/:keyword', function ($keyword) use ($app, $response) {
	$db = DBConnection();
	$result = $db->query("select * from cs_user WHERE nama LIKE '%$keyword%' OR username LIKE '%$keyword%'")->fetchAll();
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API Tambah User
$app->post('/user', function () use ($app, $response) {
	$db = DBConnection();
	$nama 		= $app->request->post('nama');
	$username 	= strtolower($app->request->post('username'));
	$pass 		= base64_encode($app->request->post('pass'));
	$level 		= strtoupper($app->request->post('level'));

	$sql = "INSERT INTO cs_user (nama, username, pass, level) VALUE (:nama, :username, :pass, :level)";
    $stmt = $db->prepare($sql);
	$data = [
        ":nama"     => $nama,
        ":username" => $username,
        ":pass"     => $pass,
        ":level"    => $level
    ];

    if($stmt->execute($data)){
    	$result["success"] = true;
		$result["message"] = "Success insert data";
		
    }
	else{
		$result["success"] = false;
		$result["message"] = $response;
	}
		
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API Update User
$app->put('/user/:id', function ($id) use ($app, $response) {
	$db = DBConnection();
	$nama 		= $app->request->put('nama');
	$pass 		= base64_encode($app->request->put('pass'));
	$level 		= strtoupper($app->request->put('level'));

	$sql = "UPDATE cs_user SET nama=:nama, pass=:pass, level=:level WHERE id=:id";
    $stmt = $db->prepare($sql);
    
    $data = [
    	":id" 		=> $id,
        ":nama"     => $nama,
        ":pass"     => $pass,
        ":level"    => $level
    ];

    if($stmt->execute($data)){
    	$result["success"] = true;
		$result["message"] = "Success update data";
    }
	else{
		$result["success"] = faild;
		$result["message"] = "Gagal update data";
	}

		
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API Delete User
$app->delete('/user/delete/:id', function ($id) use ($app, $response) {
	$db = DBConnection();

	$sql = "DELETE FROM cs_user WHERE id=:id";
    $stmt = $db->prepare($sql);
	$data = [
                ":id" => $id
            ];

    if($stmt->execute($data)){
    	$result["status"] = true;
		$result["message"] = "Success delete data";
    }
	else{
		$result["status"] = false;
		$result["message"] = "Failed delete data";
	}
	
	$response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API Login
$app->post('/api/login', function () use ($app, $response) {
	$db = DBConnection();
	$username 	= $app->request->post('username');
	$pass 		= base64_encode($app->request->post('pass'));

	if (empty($username) || empty($pass)) {
        $result = array(
            "status" => false,
            "message" => "Username dan Password tidak boleh kosong"
        );
    }
    else{
    	$sql = "SELECT * FROM cs_user WHERE pass=:pass AND username=:username";
    	$stmt = $db->prepare($sql);
    	$data = [
	        ":username" => $username,
	        ":pass"     => $pass
	    ];
	    $stmt->execute($data);

	    if($stmt->rowCount() > 0){
	    	$result = array(
	            "status" => true,
	            "message" => "Login Sukses"
	        );
	    }
		else{
			$result = array(
	            "status" => false,
	            "message" => "Login Gagal"
	        );
		}
    }
    
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode($result));
});

// API Laporan Penjualan



function DBConnection(){
	return new PDO('mysql:dbhost=localhost;dbname=coffee_shop','root','');
};

$app->run();
