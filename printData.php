<?php

$mongoConnection = new Mongo();
$passivCollection = $mongoConnection->seih->passiv;

$startTime = DateTime::createFromFormat("d/m-Y H:i", "03/08-2013 00:00");
$endTime = DateTime::createFromFormat("d/m-Y H:i", "03/08-2013 23:59");

$condition = array('homeId' => 38959, 'date' => array('$gte' => new MongoDate($startTime->format('U')), '$lt' => new MongoDate($endTime->format('U'))));
$cursor = $passivCollection->find($condition);
print 'Found ' . $cursor->count() . ' entries.' . PHP_EOL;
foreach($cursor as $entry) {
	$date = DateTime::createFromFormat('U', $entry['date']->sec);
	print 'HomeID: '. $entry['homeId'] . ' Sensor: ' .$entry['sensor'] . ' Measured on ' . $date->format('d/m-Y H:i:s') . 'Value: ' . $entry['val'] . PHP_EOL;
}
$mongoConnection->close();
