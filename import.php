<?php

$fileName = '/Users/revsbech/PhpstormProjects/Seih/Data/Passiv/2013-31/data/DP004-2013-31-hot-water-cylinder-in-pipe-temperature.csv';

$row = 1;
if (($handle = fopen($fileName, "r")) !== FALSE) {

	$mongoConnection = new Mongo();
	$passivCollection = $mongoConnection->seih->passiv;
	$passivCollection->ensureIndex(array('homeId' => 1, 'date' => 1));
	$passivCollection->ensureIndex(array('date' => 1));
	$passivCollection->ensureIndex(array('sensor' => 1, 'date' => 1, 'homeId' => 1), array ('unique' => TRUE));
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		$row++;

		$dataForMongo = array();
		$dataForMongo['homeId'] = intval($data[0]);
		$dataForMongo['sensor'] = createAbbrevationFromFullName($data[1]);
		$date =  DateTime::createFromFormat('d/m/Y H:i:s', $data[2]);
		if ($date === FALSE) {
			exit ("Unable to parse " . $data[2]);
		}
		$dataForMongo['date'] =  new MongoDate($date->format('U'));
		$indexData = $dataForMongo;

		$dataForMongo['val'] = $data[3];
		$passivCollection->update($indexData, $dataForMongo, array('upsert' => TRUE));

		if ($row > 5) {
			break;
		}
	}
	print "Imported " . $row . " entries." . PHP_EOL;
	fclose($handle);
}

/**
 * @param $fullName
 * ö®eturn string
 */
function createAbbrevationFromFullName($fullName) {
	$items = explode(' ', $fullName);
	$abbrevation = '';
	foreach ($items as $item) {
		$abbrevation .= $item[0];
	}
	return $abbrevation;
}

//
//db.passiv.find({date: {$gte: datetime(2013,07,29)}}).count();