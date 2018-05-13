<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

//product
$app->get("/products/", function (Request $request, Response $response){
    $sql = "SELECT * FROM product";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});


$app->get("/products/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "SELECT * FROM products WHERE ID_PRODUCT=:id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->post("/products/", function (Request $request, Response $response){

    $new_product = $request->getParsedBody();

    $sql = "INSERT INTO product (ID_PRODUCT, NAMA, HARGA) VALUE (:id_product, :nama, :harga)";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":id_product" => $new_product["id_product"],
        ":nama" => $new_product["nama"],
        ":harga" => $new_product["harga"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->put("/products/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $new_product = $request->getParsedBody();
    $sql = "UPDATE product SET NAMA=:nama, HARGA=:harga WHERE ID_PRODUCT=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id,
        ":nama" => $new_product["nama"],
        ":harga" => $new_product["harga"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->delete("/products/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "DELETE FROM product WHERE ID_PRODUCT=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

//kurir
$app->get("/kurir/", function (Request $request, Response $response){
    $sql = "SELECT * FROM kurir";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});


$app->get("/kurir/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "SELECT * FROM kurir WHERE ID_KURIR=:id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->post("/kurir/", function (Request $request, Response $response){

    $new_kurir = $request->getParsedBody();

    $sql = "INSERT INTO kurir (ID_KURIR, NAMA, NO_KTP, NO_HP) VALUE (:id_kurir, :nama, :no_ktp, no_hp)";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":id_kurir" => $new_kurir["id_kurir"],
        ":nama" => $new_kurir["nama"],
        ":no_ktp" => $new_kurir["no_ktp"],
        ":no_hp" => $new_kurir["no_hp"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->put("/kurir/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $new_kurir = $request->getParsedBody();
    $sql = "UPDATE kurir SET NAMA=:nama, NO_KTP=:no_ktp, NO_HP=:no_hp WHERE ID_KURIR=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id,
        ":nama" => $new_kurir["nama"],
        ":no_ktp" => $new_kurir["no_ktp"],
        ":no_hp" => $new_kurir["no_hp"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->delete("/kurir/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "DELETE FROM kurir WHERE ID_KURIR=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->post('/kurir/foto/{id}', function(Request $request, Response $response, $args) {
    
    $uploadedFiles = $request->getUploadedFiles();
    
    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['foto'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        
        // ubah nama file dengan id buku
        $filename = sprintf('%s.%0.8s', $args["id"], $extension);
        
        $directory = $this->get('settings')['upload_directory'];
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        // simpan nama file ke database
        $sql = "UPDATE kurir SET FOTO=:foto WHERE ID_KURIR=:id";
        $stmt = $this->db->prepare($sql);
        $params = [
            ":id" => $args["id"],
            ":foto" => $filename
        ];
        
        if($stmt->execute($params)){
            // ambil base url dan gabungkan dengan file name untuk membentuk URL file
            $url = $request->getUri()->getBaseUrl()."/uploads/".$filename;
            return $response->withJson(["status" => "success", "data" => $url], 200);
        }
        
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    }
});

//order product
$app->get("/order_product/", function (Request $request, Response $response){
    $sql = "SELECT * FROM order_product";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->get("/order_product/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "SELECT * FROM order_product WHERE ID_ORDER=:id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->delete("/order_product/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "DELETE FROM order_product WHERE ID_ORDER=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->post("/order_product/", function (Request $request, Response $response){

    $new_order = $request->getParsedBody();

    $sql = "INSERT INTO order_product (ID_ORDER, ID_KURIR, ID_PRODUCT, ID, TGL_ORDER, END_ORDER, MULTIMEDIA, SECURITY, OFFICE, STATUS, LONGITUDE, LATITUDE) VALUE (:id_order, :id_kurir, :id_product, :id, :tgl_order, :end_order, :multimedia, :security, :office, :status, :longitude, :latitude )";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":id_order" => $new_order["id_order"],
        ":id_kurir" => $new_order["id_kurir"],
        ":id_product" => $new_order["id_product"],
        ":id" => $new_order["id"],
        ":tgl_order" => $new_order["tgl_order"],
        ":end_order" => $new_order["end_order"],
        ":multimedia" => $new_order["multimedia"],
        ":security" => $new_order["security"],
        ":office" => $new_order["office"],
        ":status" => $new_order["status"],
        ":longitude" => $new_order["longitude"],
        ":latitude" => $new_order["latitude"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->put("/order_product/{id_order}", function (Request $request, Response $response, $args){
    $id_order = $args["id_order"]; 
    $new_order = $request->getParsedBody();
    $sql = "UPDATE order_product SET ID_KURIR=:id_kurir, ID_PRODUCT=:id_product, ID=:id, TGL_ORDER=:tgl_order, END_ORDER=:end_order, MULTIMEDIA=:multimedia, SECURITY=:security, OFFICE=:office, STATUS=:status, LONGITUDE=:longitude, LATITUDE=:LATITUDE WHERE ID_ORDER=:id_order";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id_order" => $id_order,
        ":id_kurir" => $new_order["id_kurir"],
        ":id_product" => $new_order["id_product"],
        ":id" => $new_order["id"],
        ":tgl_order" => $new_order["tgl_order"],
        ":end_order" => $new_order["end_order"],
        ":multimedia" => $new_order["multimedia"],
        ":security" => $new_order["security"],
        ":office" => $new_order["office"],
        ":status" => $new_order["status"],
        ":longitude" => $new_order["longitude"],
        ":latitude" => $new_order["latitude"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});