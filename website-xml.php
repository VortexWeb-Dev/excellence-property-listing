<?php
require 'utils/index.php';
require_once __DIR__ . "/crest/settings.php";

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = WEB_HOOK_URL;
$entityTypeId = LISTINGS_ENTITY_TYPE_ID;
$fields = [
    'id',
    'ufCrm6ReferenceNumber',
    'ufCrm6PermitNumber',
    'ufCrm6ReraPermitNumber',
    'ufCrm6DtcmPermitNumber',
    'ufCrm6OfferingType',
    'ufCrm6PropertyType',
    'ufCrm6HidePrice',
    'ufCrm6RentalPeriod',
    'ufCrm6Price',
    'ufCrm6ServiceCharge',
    'ufCrm6NoOfCheques',
    'ufCrm6City',
    'ufCrm6Community',
    'ufCrm6SubCommunity',
    'ufCrm6Tower',
    'ufCrm6TitleEn',
    'ufCrm6TitleAr',
    'ufCrm6DescriptionEn',
    'ufCrm6DescriptionAr',
    'ufCrm6TotalPlotSize',
    'ufCrm6Size',
    'ufCrm6Bedroom',
    'ufCrm6Bathroom',
    'ufCrm6AgentId',
    'ufCrm6AgentName',
    'ufCrm6AgentEmail',
    'ufCrm6AgentPhone',
    'ufCrm6AgentPhoto',
    'ufCrm6BuildYear',
    'ufCrm6Parking',
    'ufCrm6Furnished',
    'ufCrm_6_360_VIEW_URL',
    'ufCrm6PhotoLinks',
    'ufCrm6FloorPlan',
    'ufCrm6Geopoints',
    'ufCrm6AvailableFrom',
    'ufCrm6VideoTourUrl',
    'ufCrm6Developers',
    'ufCrm6ProjectName',
    'ufCrm6ProjectStatus',
    'ufCrm6ListingOwner',
    'ufCrm6Status',
    'ufCrm6PfEnable',
    'ufCrm6BayutEnable',
    'ufCrm6DubizzleEnable',
    'ufCrm6WebsiteEnable',
    'updatedTime',
    'ufCrm6TitleDeed',
    'ufCrm6PrivateAmenities'
];

$properties = fetchAllProperties($baseUrl, $entityTypeId, $fields, 'website');

if (count($properties) > 0) {
    $xml = generateWebsiteXml($properties);
    echo $xml;
} else {
    echo '<?xml version="1.0" encoding="UTF-8"?><list></list>';
}
