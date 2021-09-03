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
		case 'insertResultadoEvaluacion':
			$puntaje = $_REQUEST['puntaje'];
			$sesskey = $_REQUEST['sesskey'];
			$returnArr = insertResultadoEvaluacion($puntaje, $sesskey);
			break;
		case 'getMateriales':
			$returnArr = getMateriales();
			break;
		case 'materialesMarcadosByUser':
			$materialid = $_REQUEST['materialid'];
			$sesskey = $_REQUEST['sesskey'];
			$returnArr = materialesMarcadosByUser($materialid, $sesskey);
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
 * ? se deberia excluir las preguntas que ya fueron respondidas?
 * ? se deberia mostrar la puntuacion de la pregunta si ya fue marcada?
 */
function getPreguntasEncuesta() {
	global $DB, $USER;
	$not_in = [];
	$if_exists = $DB->get_records('aq_encuesta_user_data', [
		'userid' => $USER->id
	]);
	if(count($if_exists)){
		foreach ($if_exists as $key => $value) {
			array_push($not_in, $value->preguntaid);
		}
	}
	// TODO: si se decide excluir las preguntas entonces hacer un RAW SQL QUERY 
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

	$if_exists = $DB->get_records('aq_encuesta_user_data', [
		'userid' => $USER->id,
		'preg_encuestaid' => $id,
	]);

	if(count($if_exists)){
		foreach ($if_exists as $key => $value) {
			$data = array(
				'id' => $value->id,
				'puntaje' => $puntaje,
				'updated_at' => time()
			);
			$DB->update_record('aq_encuesta_user_data', $data);
		}
		return 'updated';
	}else{
		$data = array(
			'userid' => $USER->id,
			'preg_encuestaid' => $id,
			'puntaje' => $puntaje,
			'created_at' => time()
		);
		$insert_id = $DB->insert_record('aq_encuesta_user_data', $data);
		return 'inserted';
	}
}

/**
 * getPreguntasOpcionesEvaluacion
 * * obtiene las preguntas de la evaluacion y sus opciones
 */
function getPreguntasOpcionesEvaluacion(){
	global $DB, $USER;

	$result = $DB->get_field('aq_eval_user_puntaje_data', 'puntaje_porcentaje', [
		'userid' => $USER->id
	]);

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

	$output = [
		'preguntas' => $data,
		'result' => $result == false ? 0 : intval($result) 
	];

	return $output;
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

	$if_exists = $DB->get_records('aq_eval_user_puntaje_data', [
		'userid' => $USER->id
	]);

	if(count($if_exists)){
		foreach ($if_exists as $key => $value) {
			$data = array(
				'id' => $value->id,
				'puntaje_porcentaje' => $value->puntaje_porcentaje > 80 ? $value->puntaje_porcentaje : $puntaje,
				'created_at' => time()
			);
			$DB->update_record('aq_eval_user_puntaje_data', $data);
		}
		return 'updated';
	}else{
		$data = array(
			'userid' => $USER->id,
			'puntaje_porcentaje' => $puntaje,
			'created_at' => time()
		);
		$insert_id = $DB->insert_record('aq_eval_user_puntaje_data', $data);
		return 'inserted';
	}
}

/**
 * getMateriales
 * * obtiene los registros para la actividad revision material
 */
function getMateriales(){
	global $DB, $USER;

	$data = [];
	$materiales = $DB->get_records('aq_material_data', [
		'active' => 1
	]);

	foreach ($materiales as $key => $value) {
		$if_marked = $DB->get_records('aq_material_revisado_data', [
			'userid' => $USER->id,
			'materialid' => $value->id
		]);
		array_push($data, [
			'id' => $value->id,
			'material_title' => $value->material_title,
			'material_icon' => $value->material_icon,
			'link_file' => $value->link_file,
			'format' => $value->format,
			'marked' => count($if_marked) ? true : false

		]);
	}
	return $data;
}

function materialesMarcadosByUser($materialid, $sesskey){
	global $DB, $USER;
	require_sesskey();

	$if_marked = $DB->get_records('aq_material_revisado_data', [
		'userid' => $USER->id,
		'materialid' => $materialid
	]);

	if(count($if_marked)){
		foreach ($if_marked as $key => $value) {
			$data = array(
				'id' => $value->id,
				'userid' => $USER->id,
				'materialid' => $materialid,
				// 'updated_at' => time()
			);
			$DB->delete_records('aq_material_revisado_data', $data);
		}
		return 'updated';
	}else{
		$data = array(
			'userid' => $USER->id,
			'materialid' => $materialid,
			'created_at' => time()
		);
		$insert_id = $DB->insert_record('aq_material_revisado_data', $data);
		return 'inserted';
	}
}