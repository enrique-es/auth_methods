<?php

header('Content-Type: application/json');
//Identificación por user and password en el header de la peticion (Autenticación via HTTP)

$user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : '';
$password = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW'] : '';

if($user !== 'enrique' && $password !== '123456'){
    echo "El usuario no tiene acceso";
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
        'id_genere' => '0'
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
            switch ($resourceType){
                case 'books':
                    echo json_encode($books);
                    break;
                case 'authors':
                    echo json_encode($authors);
                    break;
                case 'generes':
                    echo json_encode($generes);
                    break;
                }
        }else{        
            if($resourceType == "books" && array_key_exists($resource_id, $books)){
                echo json_encode($books[$resource_id]);
            }elseif($resourceType == "authors" &&array_key_exists($resource_id, $authors)){
                echo json_encode($authors[$resource_id]);
            }elseif($resourceType == "generes" && array_key_exists($resource_id, $generes)){
                echo json_encode($generes[$resource_id]);
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