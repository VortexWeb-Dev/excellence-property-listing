<?php

require __DIR__ . "/crest/crest.php";
require __DIR__ . "/crest/crestcurrent.php";
require __DIR__ . "/crest/settings.php";
require __DIR__ . "/utils/index.php";
require __DIR__ . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id = $_GET['id'];

$response = CRest::call('crm.item.list', [
    "entityTypeId" => LISTINGS_ENTITY_TYPE_ID,
    "filter" => ["id" => $id],
    "select" => [
        "ufCrm6ReferenceNumber",
        "ufCrm6OfferingType",
        "ufCrm6PropertyType",
        "ufCrm6SaleType",
        "ufCrm6UnitNo",
        "ufCrm6Size",
        "ufCrm6Bedroom",
        "ufCrm6Bathroom",
        "ufCrm6Parking",
        "ufCrm6LotSize",
        "ufCrm6TotalPlotSize",
        "ufCrm6BuildupArea",
        "ufCrm6LayoutType",
        "ufCrm6TitleEn",
        "ufCrm6DescriptionEn",
        "ufCrm6TitleAr",
        "ufCrm6DescriptionAr",
        "ufCrm6Geopoints",
        "ufCrm6ListingOwner",
        "ufCrm6LandlordName",
        "ufCrm6LandlordEmail",
        "ufCrm6LandlordContact",
        "ufCrm6ReraPermitNumber",
        "ufCrm6ReraPermitIssueDate",
        "ufCrm6ReraPermitExpirationDate",
        "ufCrm6DtcmPermitNumber",
        "ufCrm6Location",
        "ufCrm6BayutLocation",
        "ufCrm6ProjectName",
        "ufCrm6ProjectStatus",
        "ufCrm6Ownership",
        "ufCrm6Developers",
        "ufCrm6BuildYear",
        "ufCrm6Availability",
        "ufCrm6AvailableFrom",
        "ufCrm6RentalPeriod",
        "ufCrm6Furnished",
        "ufCrm6DownPaymentPrice",
        "ufCrm6NoOfCheques",
        "ufCrm6ServiceCharge",
        "ufCrm6PaymentMethod",
        "ufCrm6FinancialStatus",
        "ufCrm6AgentName",
        "ufCrm6ContractExpiryDate",
        "ufCrm6FloorPlan",
        "ufCrm6QrCodePropertyBooster",
        "ufCrm6VideoTourUrl",
        "ufCrm_12_360_VIEW_URL",
        "ufCrm6BrochureDescription",
        "ufCrm_12_BROCHURE_DESCRIPTION_2",
        "ufCrm6PhotoLinks",
        "ufCrm6Notes",
        "ufCrm6Amenities",
        "ufCrm6Price",
        "ufCrm6Status",
        "ufCrm6HidePrice",
        "ufCrm6PfEnable",
        "ufCrm6BayutEnable",
        "ufCrm6DubizzleEnable",
        "ufCrm6WebsiteEnable",
        "ufCrm6TitleDeed",
        "ufCrm_12_LANDLORD_NAME_2",
        "ufCrm_12_LANDLORD_EMAIL_2",
        "ufCrm_12_LANDLORD_CONTACT_2",
        "ufCrm_12_LANDLORD_NAME_3",
        "ufCrm_12_LANDLORD_EMAIL_3",
        "ufCrm_12_LANDLORD_CONTACT_3"
        // "ufCrm6City",
        // "ufCrm6Community",
        // "ufCrm6SubCommunity",
        // "ufCrm6Tower",
        // "ufCrm6BayutCity",
        // "ufCrm6BayutCommunity",
        // "ufCrm6BayutSubCommunity",
        // "ufCrm6BayutTower",
        // "ufCrm6AgentId",
        // "ufCrm6AgentEmail",
        // "ufCrm6AgentPhone",
        // "ufCrm6AgentLicense",
        // "ufCrm6AgentPhoto",
        // "ufCrm6Watermark",
    ]
]);

$property = $response['result']['items'][0];

if (!$property) {
    die("Property not found.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

function getExcelColumn($index)
{
    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
}

$columnIndex = 1;
foreach ($property as $key => $value) {
    if (empty($value)) {
        continue;
    }

    $colLetter = getExcelColumn($columnIndex);
    $sheet->setCellValue($colLetter . '1', $key);
    $sheet->getStyle($colLetter . '1')->getFont()->setBold(true);
    $sheet->setCellValue($colLetter . '2', is_array($value) ? implode(', ', $value) : $value); // Values
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
    $columnIndex++;
}

function sanitizeFileName($filename)
{
    $filename = trim($filename);
    $filename = str_replace(' ', '_', $filename);
    $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $filename);
    $filename = preg_replace('/_+/', '_', $filename);

    return $filename;
}

$filename = "property_" . sanitizeFileName($property['ufCrm6ReferenceNumber']) . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
