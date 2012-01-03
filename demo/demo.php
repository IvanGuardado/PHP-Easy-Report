<?php
/**
 * Fast demo from cli writing:
 * php5 demo.php
 */
 
set_include_path(get_include_path().PATH_SEPARATOR.'../src');

require_once '../src/EasyReport.php';

$docGenerator = new EasyReport('template-demo.odt', '/tmp');

$data = array(
    'name' => 'Iván Guardado Castro',
    'link' => 'ivanguardado.com',
    'visits' => array(
        array('Nombre', 'Fecha acceso', 'Tiempo visita'),
        array('Emilio Nicolás', '20/10/2011', '5 min'),
        array('Javier López', '20/10/2011', '1m'),
        array('Adrián Mato', '19/10/2011', '2 min'),
        array('Jesús Pérez', '18/10/2011', '8 min')
));

$docGenerator->create('result/final.odt', $data);

$docGenerator->create('result/final.doc', $data);

$docGenerator->create('result/final.pdf', $data);

