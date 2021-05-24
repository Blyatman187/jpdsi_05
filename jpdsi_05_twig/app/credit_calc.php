<?php

require_once dirname(__FILE__) . '/../config.php';

require_once dirname(__FILE__) . "/../vendor/autoload.php";

$loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . "/templates");
$twig = new \Twig\Environment($loader);

function getParams(&$form)
{
	$form['kwota'] = isset($_REQUEST['kwota']) ?  $_REQUEST['kwota'] : null;
	$form['lata'] = isset($_REQUEST['lata']) ?  $_REQUEST['lata'] : null;
	$form['oprocentowanie'] = isset($_REQUEST['oprocentowanie']) ?  $_REQUEST['oprocentowanie'] : null;
}


function validate(&$form, &$messages)
{
	if (!(isset($form['kwota']) && isset($form['lata']) && isset($form['oprocentowanie']))) {
		return false;
	}


	if ($form['kwota'] == "") {
		$messages[] = 'Nie podano kwoty pozyczki';
	}
	if ($form['lata'] == "") {
		$messages[] = 'Nie podano czasu na jaki została wzięta pozyczka';
	}
	if ($form['oprocentowanie'] == "") {
		$messages[] = "Nie podano oprocentowania";
	}

	if (count($messages) != 0) {
		return false;
	}

	if (!is_numeric($form['kwota'])) {
		$messages[] = 'Kwota pozyczki nie jest liczbą całkowitą';
	}

	if (!is_numeric($form['lata'])) {
		$messages[] = 'Okres spłacenia pozyczki nie został podany jako liczba całkowita';
	}
	if (!is_numeric($form['oprocentowanie'])) {
		$messages[] = 'Oprocentowanie nie jest wartością całkowitą';
	}

	if (count($messages) != 0) {
		return false;
	} else {
		return true;
	}
}


function process(&$form, &$result)
{

	if (empty($messages)) {
		$form['kwota'] = intval($form['kwota']);

		$form['lata'] = intval($form['lata']);

		$form['oprocentowanie'] = floatval($form['oprocentowanie']);

		$result = ($form['kwota'] + $form['kwota'] * $form['oprocentowanie']) / (12 * $form['lata']);
	}
}

$form = null;
$result = null;
$messages = array();

getParams($form);

if (validate($form, $messages)) {
	process($form, $result);
}

echo $twig->render('credit_calc.html.twig', array(
	"string_array" => array(
		"result_desc" => "Miesięczna rata to",
		"space" => " ",
		"value" => "zł"
	),
	"form" => $form,
	"result" => $result,
	"messages" => $messages
));
