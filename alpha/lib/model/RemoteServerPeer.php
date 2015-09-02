<?php


/**
 * Skeleton subclass for performing query and update operations on the 'remote_server' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class RemoteServerPeer extends BaseRemoteServerPeer {
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
	
		$c = KalturaCriteria::create(RemoteServerPeer::OM_CLASS);
		$c->addAnd ( RemoteServerPeer::STATUS, RemoteServerStatus::DELETED, Criteria::NOT_EQUAL);
	
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function retrieveByPartnerIdAndHostName($partnerId, $hostName)
	{
		$c = new Criteria();
	
		$c->add(RemoteServerPeer::PARTNER_ID, $partnerId);
		$c->add(RemoteServerPeer::HOST_NAME, $hostName);
	
		return RemoteServerPeer::doSelectOne($c);
	}
	
	public static function retrieveOrderedEdgeServersArrayByPKs($pks, PropelPDO $con = null)
	{
		if (empty($pks)) {
			$objs = array();
		}
		else {
			$criteria = new Criteria(RemoteServerPeer::DATABASE_NAME);
			$criteria->add(RemoteServerPeer::ID, $pks, Criteria::IN);
			$criteria->add(RemoteServerPeer::STATUS, RemoteServerStatus::ACTIVE);
			$orderBy = "FIELD (" . self::ID . "," . implode(",", $pks) . ")";  // first take the pattner_id and then the rest
			$criteria->addAscendingOrderByColumn($orderBy);
			$objs = RemoteServerPeer::doSelect($criteria, $con);
		}
	
		return $objs;
	}

} // RemoteServerPeer
