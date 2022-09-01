<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

//Поиск второго по встречаемости символа в строке
$app->get('/get_second_symbol_by_popularity/{string}', function (Request $request, Response $response, $args) {
    $string = $args['string'];
    $countOfSymbols = array_count_values(str_split($string));
    
    if(sizeof($countOfSymbols) < 2) {
    	$response->getBody()->write("Ошибка. Строка состоит меньше чем из 2 уникальных символов\n");
    	return $response;
	}
	
	arsort($countOfSymbols);
	$mostPopularSymbol = array_key_first($countOfSymbols);
	$mostPopularCount = $countOfSymbols[$mostPopularSymbol];
	$symbols = [];
	$secondPopularCount = null;
	foreach($countOfSymbols as $symbol => $count) {
		if(is_null($secondPopularCount) && $mostPopularCount > $count)
			$secondPopularCount = $count;
		if($count === $secondPopularCount)
			$symbols[] = $symbol;
	}
	if(empty($symbols)) {
    	$response->getBody()->write("В строке отсутствует второй по встречаемости символ.\n");
    	return $response;
	}
	$result = implode('', $symbols);
	$response->getBody()->write($result."\n");
    return $response;
});

//Проверка, является ли строка полиндромом
$app->get('/is_palindrome/{string}', function (Request $request, Response $response, $args) {
    $string = $args['string'];
    if(empty($string)) {
    	$response->getBody()->write("Ошибка. Передана пустая строка\n");
    	return $response;
	}
    $reverse = strrev($string);
    $result = ($string === $reverse ? "палиндром" : "не палиндром");
    $response->getBody()->write($result."\n");
    return $response;
});

$app->run();