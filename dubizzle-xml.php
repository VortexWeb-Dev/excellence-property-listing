<?php
require 'utils/index.php';
require_once __DIR__ . "/crest/settings.php";

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = WEB_HOOK_URL;
$entityTypeId = LISTINGS_ENTITY_TYPE_ID;
$fields = [
    'id',
    'ufCrm12ReferenceNumber',
    'ufCrm12PermitNumber',
    'ufCrm12ReraPermitNumber',
    'ufCrm12DtcmPermitNumber',
    'ufCrm12OfferingType',
    'ufCrm12PropertyType',
    'ufCrm12HidePrice',
    'ufCrm12RentalPeriod',
    'ufCrm12Price',
    'ufCrm12ServiceCharge',
    'ufCrm12NoOfCheques',
    'ufCrm12City',
    'ufCrm12Community',
    'ufCrm12SubCommunity',
    'ufCrm12Tower',
    'ufCrm12BayutCity',
    'ufCrm12BayutCommunity',
    'ufCrm12BayutSubCommunity',
    'ufCrm12BayutTower',
    'ufCrm12TitleEn',
    'ufCrm12TitleAr',
    'ufCrm12DescriptionEn',
    'ufCrm12DescriptionAr',
    'ufCrm12TotalPlotSize',
    'ufCrm12Size',
    'ufCrm12Bedroom',
    'ufCrm12Bathroom',
    'ufCrm12AgentId',
    'ufCrm12AgentName',
    'ufCrm12AgentEmail',
    'ufCrm12AgentPhone',
    'ufCrm12AgentPhoto',
    'ufCrm12BuildYear',
    'ufCrm12Parking',
    'ufCrm12Furnished',
    'ufCrm_12_360_VIEW_URL',
    'ufCrm12PhotoLinks',
    'ufCrm12FloorPlan',
    'ufCrm12Geopoints',
    'ufCrm12AvailableFrom',
    'ufCrm12VideoTourUrl',
    'ufCrm12Developers',
    'ufCrm12ProjectName',
    'ufCrm12ProjectStatus',
    'ufCrm12ListingOwner',
    'ufCrm12Status',
    'ufCrm12PfEnable',
    'ufCrm12BayutEnable',
    'ufCrm12DubizzleEnable',
    'ufCrm12SaleType',
    'ufCrm12WebsiteEnable',
    'updatedTime',
    'ufCrm12TitleDeed',
    'ufCrm12Amenities'
];

$properties = fetchAllProperties($baseUrl, $entityTypeId, $fields, 'dubizzle');

if (count($properties) > 0) {
    $xml = generateBayutXml($properties);
    echo $xml;
} else {
    echo '<?xml version="1.0" encoding="UTF-8"?><list></list>';
}
