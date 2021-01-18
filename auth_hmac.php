<?php

header('Content-Type: application/json');
//Identificación por user and password en el header de la peticion (Autenticación via HMAC)
if(
    !array_key_exists('HTTP_X_HASH', $_SERVER) ||
    !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
    !array_key_exists('HTTP_X_UID', $_SERVER) 
){
    die;
}
list($hash, $uid, $timestamp) = [
    $_SERVER['HTTP_X_HASH'],
    $_SERVER['HTTP_X_UID'],
    $_SERVER['HTTP_X_TIMESTAMP'],
];

$secret = 'string_secreto';
$newHash = sha1($uid.$timestamp.$secret);

if ($newHash !== $hash){
    echo 'autenticacion erronea';
    die;
}

//Definimos los recursos disponibles
$allowResourceTypes = [
    'books',
    'authors',
    'generes'
];

//Validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];

if(!in_array ($resourceType, $allowResourceTypes )){
    die;
}

//Defino los recursos

$books = [
    0 => [
        'title' => '100 años de soledad',
        'id_author' => '0',
        'id_genere' => '1'
    ],
    1 => [
        'titulo' => 'Homodeus',
        'id_author' => '1',
        'id_genere' => ''
    ],
    2 => [
        'titulo' => 'Sapiens',
        'id_author' => '1',
        'id_genere' => '0'
    ]
];
$authors = [
    0 => [
        'id_author' => '0',
        'aut´hor' => 'Octavio Paz'        
    ],
    1 => [
        'id_author' => '1',
        'author' => 'Yuval Noah Harari'        
    ]
];
$generes = [
    0 => [
        'id_genere' => '0',
        'genere' => 'ensayo'
    ],
    1 => [
        'id_genere' => '1',
        'genere' => 'novela'
    ],
    2 => [
        'id_genere' => '2',
        'genere' => 'sic-fic'
    ]
];


//traemos el id del recurso buscado
///$resource_id = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : ''; 
if(isset($_GET['resource_id'])){
    $resource_id = $_GET['resource_id'];
} else{
    $resource_id = '';
}
//Generamos la respuesta asumiendo que el pedido es correcto
switch ( strtoupper( $_SERVER['REQUEST_METHOD'] )) {
    case 'GET':
        if (empty($resource_id)){
            echo json_encode($books); 
        }else{
            if(array_key_exists($resource_id, $books)){
                echo json_encode($books[$resource_id]);
            }
        }
        
        //echo "im in get";
        break;
    case 'POST':
        //echo "im in post";
        $json = file_get_contents('php://input');
        $books[] = json_decode($json, true);
        //echo array_keys($books)[count($books) - 1];
        echo json_encode($books);
        break;
    case 'PUT':
        //validamos que el recurso buscado exista
        if (!empty($resource_id) && array_key_exists($resource_id, $books)){
            //tomamos la entrada cruda
            $json = file_get_contents('php://input');
            //convertimos a arreglo la entrada y la asiganos al recurso indicado
            $books[$resource_id] = json_decode($json, true);
            //retornamos la información modificada en formato json
            echo json_encode($books);
        }
        break;
    case 'DELETE':
        //validamos que el recurso exista
        if (!empty($resource_id) && array_key_exists($resource_id, $books)){
            //eliminamos el recurso
            unset($books[$resource_id]);       

        } 
        echo json_encode($books);
        break;
}

?>