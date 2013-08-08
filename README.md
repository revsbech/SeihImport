SeihImport
==========

ProofOfConcept for Seih MongoImport


Examples of MongoQuery
----------------------

Example, using index to find all measurements for a given homeId within a date range

 db.passiv.find({homeId: 38959, date: {$gte: new ISODate("2013-07-29T00:00:00Z"), $lt: ISODate("2013-07-30T00:00:00Z")}});

In PHP this translates to

 $mongoConnection = new Mongo();
 $passivCollection = $mongoConnection->seih->passiv;
 $startTime = DateTime::createFromFormat("d/m-Y H:i:s", "29/07-2013 00:00:00");
 $endTime = DateTime::createFromFormat("d/m-Y H:i:s", "30/07-2013 00:00:00");
 $condition = array('homeId' => 38959, 'date' => array('$gte' => new MongoDate($startTime->format('U')), '$lt' => new MongoDate($endTime->format('U'))));
 $cursor = $passivCollection->find($condition);

