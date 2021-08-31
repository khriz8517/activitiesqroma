<?php 

require_once(__DIR__ . '/../../config.php');
global $DB;

require_login();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/activitiesqroma/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Activities Qroma Plugin');
$PAGE->set_heading('Activities Qroma Plugin');
// $PAGE->requires->js_call_amd('local_activitiesqroma/confirm');


$templateContext = (object)[
    'sesskey' => sesskey()
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_activitiesqroma/index', $templateContext);
echo $OUTPUT->footer();