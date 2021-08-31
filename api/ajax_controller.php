<?php

// use core_completion\progress;
// use core_course\external\course_summary_exporter;

error_reporting(E_ALL);
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/enrol/externallib.php');

try {
	global $USER, $PAGE;
	$details = $_POST;
	$returnArr = array();

	if (!isset($_REQUEST['request_type']) || strlen($_REQUEST['request_type']) == false) {
		throw new Exception();
	}

	switch ($_REQUEST['request_type']) {
		case 'getPreguntasEncuesta':
			$returnArr = getPreguntasEncuesta();
			break;
		case 'encuestaRespByUser':
			$id = $_REQUEST['id'];
			$puntaje = $_REQUEST['puntaje'];
			$sesskey = $_REQUEST['sesskey'];
			$returnArr = encuestaRespByUser($id, $puntaje, $sesskey);
			break;
		case 'getPreguntasOpcionesEvaluacion':
			$returnArr = getPreguntasOpcionesEvaluacion();
			break;
		case 'getMateriales':
			$returnArr = getMateriales();
			break;
	}

} catch (Exception $e) {
	$returnArr['status'] = false;
	$returnArr['data'] = $e->getMessage();
}

header('Content-type: application/json');

echo json_encode($returnArr);
exit();

/** 
 * getPreguntasEncuesta
 * * obtengo las pregunta de la encuesta 
 */
function getPreguntasEncuesta() {
	global $DB;
	return $DB->get_records('aq_encuesta_data', [
		'active' => 1
	]);
}

/**
 * encuestaRespByUser
 * * guarda las respuestas del usuario
 * @param id es el id de la pregunta
 * @param puntaje es el puntaje de la pregunta
 * @param sesskey es la sesion del usuario
 */
function encuestaRespByUser($id, $puntaje, $sesskey){
	global $DB, $USER;
	require_sesskey();
	$data = array(
		'id' => $id,
		'puntaje' => $puntaje
	);
	return $data;
}

/**
 * getPreguntasOpcionesEvaluacion
 * * obtiene las preguntas de la evaluacion y sus opciones
 */
function getPreguntasOpcionesEvaluacion(){
	global $DB;
	$data = [];
	$preguntas = $DB->get_records('aq_evaluacion_data', [
		'active' => 1
	]);

	foreach ($preguntas as $key => $value) {
		array_push($data, (object) array(
			'id' => $value->id,
			'pregunta' => $value->pregunta,
			'opciones' => $DB->get_records('aq_evaluacion_options_data',[
				'preguntaid' => $value->id,
				'active' => 1
			], null, 'id, opcion, preguntaid, is_valid, active')
		));
	}

	return $data;
}

/**
 * insertResultadoEvaluacion
 * * guarda el resultado de la evaluacion del usuario
 * ! EL PUNTAJE ES PORCENTUAL
 * @param puntaje es el puntaje obtenido por el usuario 
 * @param sesskey es la sesion del usuario
 */
function insertResultadoEvaluacion($puntaje, $sesskey){
	global $DB, $USER;
	require_sesskey();
}

/**
 * getMateriales
 * * 
 */
function getMateriales(){
	global $DB;
	return $DB->get_records('aq_material_data', [
		'active' => 1
	]);
}