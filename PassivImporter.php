<?php

class PassivImporter {

	/**
	 * @var Mongo
	 */
	protected $mongoConnection;

	/**
	 *
	 */
	function __construct() {
		$this->mongoConnection =  new Mongo();
	}

	/**
	 * @param $directory
	 * @return boolean
	 */
	public function importFromDirectory($directory) {
		print "Importing from directory: " . $directory . PHP_EOL;
		foreach (glob($directory . '*.csv') as $file) {
			try {
				$this->importSingeFile($file);
			} catch (Exception $e) {
				print '    Error: ' . $e->getMessage() . PHP_EOL;
			}
		};
		return TRUE;
	}

	/**
	 * @param $filename
	 * @return void
	 */
	protected function importSingeFile($fileName) {
		print "  Importing from file " . $fileName . PHP_EOL;
		$row = 1;
		if (($handle = fopen($fileName, "r")) !== FALSE) {

			$passivCollection = $this->mongoConnection->seih->passiv;
			$passivCollection->ensureIndex(array('homeId' => 1, 'date' => 1));
			$passivCollection->ensureIndex(array('date' => 1));
			$passivCollection->ensureIndex(array('sensor' => 1, 'date' => 1, 'homeId' => 1), array ('unique' => TRUE));
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				if ($num !== 4) {
					throw new Exception('file ' . $fileName . ' contains a line (lineid ' . $row . ') with only ' . $num . ' columns, expected 4.');
				}
				$row++;

				$dataForMongo = array();
				$dataForMongo['homeId'] = intval($data[0]);
				$dataForMongo['sensor'] = $this->createAbbrevationFromFullName($data[1]);
				$date =  DateTime::createFromFormat('d/m/Y H:i:s', $data[2]);
				if ($date === FALSE) {
					throw new Exception('Unable to parse ' . $data[2] . ' as date on line ' . $num . ' in file ' . $fileName);
				}
				$dataForMongo['date'] =  new MongoDate($date->format('U'));
				$indexData = $dataForMongo;

				$dataForMongo['val'] = $data[3];
				$passivCollection->update($indexData, $dataForMongo, array('upsert' => TRUE));
			}
			print "    Imported " . intval($row -1) . " entries." . PHP_EOL;
			fclose($handle);
		}
	}

	/**
	 * @param $fullName
	 * @return string
	 */
	protected function createAbbrevationFromFullName($fullName) {
		$items = explode(' ', $fullName);
		$abbrevation = '';
		foreach ($items as $item) {
			$abbrevation .= $item[0];
		}
		return $abbrevation;
	}


}
