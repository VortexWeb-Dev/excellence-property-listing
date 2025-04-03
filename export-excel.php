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
        "ufCrm12ReferenceNumber",
        "ufCrm12OfferingType",
        "ufCrm12PropertyType",
        "ufCrm12SaleType",
        "ufCrm12UnitNo",
        "ufCrm12Size",
        "ufCrm12Bedroom",
        "ufCrm12Bathroom",
        "ufCrm12Parking",
        "ufCrm12LotSize",
        "ufCrm12TotalPlotSize",
        "ufCrm12BuildupArea",
        "ufCrm12LayoutType",
        "ufCrm12TitleEn",
        "ufCrm12DescriptionEn",
        "ufCrm12TitleAr",
        "ufCrm12DescriptionAr",
        "ufCrm12Geopoints",
        "ufCrm12ListingOwner",
        "ufCrm12LandlordName",
        "ufCrm12LandlordEmail",
        "ufCrm12LandlordContact",
        "ufCrm12ReraPermitNumber",
        "ufCrm12ReraPermitIssueDate",
        "ufCrm12ReraPermitExpirationDate",
        "ufCrm12DtcmPermitNumber",
        "ufCrm12Location",
        "ufCrm12BayutLocation",
        "ufCrm12ProjectName",
        "ufCrm12ProjectStatus",
        "ufCrm12Ownership",
        "ufCrm12Developers",
        "ufCrm12BuildYear",
        "ufCrm12Availability",
        "ufCrm12AvailableFrom",
        "ufCrm12RentalPeriod",
        "ufCrm12Furnished",
        "ufCrm12DownPaymentPrice",
        "ufCrm12NoOfCheques",
        "ufCrm12ServiceCharge",
        "ufCrm12PaymentMethod",
        "ufCrm12FinancialStatus",
        "ufCrm12AgentName",
        "ufCrm12ContractExpiryDate",
        "ufCrm12FloorPlan",
        "ufCrm12QrCodePropertyBooster",
        "ufCrm12VideoTourUrl",
        "ufCrm_12_360_VIEW_URL",
        "ufCrm12BrochureDescription",
        "ufCrm_12_BROCHURE_DESCRIPTION_2",
        "ufCrm12PhotoLinks",
        "ufCrm12Notes",
        "ufCrm12Amenities",
        "ufCrm12Price",
        "ufCrm12Status",
        "ufCrm12HidePrice",
        "ufCrm12PfEnable",
        "ufCrm12BayutEnable",
        "ufCrm12DubizzleEnable",
        "ufCrm12WebsiteEnable",
        "ufCrm12TitleDeed",
        "ufCrm_12_LANDLORD_NAME_2",
        "ufCrm_12_LANDLORD_EMAIL_2",
        "ufCrm_12_LANDLORD_CONTACT_2",
        "ufCrm_12_LANDLORD_NAME_3",
        "ufCrm_12_LANDLORD_EMAIL_3",
        "ufCrm_12_LANDLORD_CONTACT_3"
        // "ufCrm12City",
        // "ufCrm12Community",
        // "ufCrm12SubCommunity",
        // "ufCrm12Tower",
        // "ufCrm12BayutCity",
        // "ufCrm12BayutCommunity",
        // "ufCrm12BayutSubCommunity",
        // "ufCrm12BayutTower",
        // "ufCrm12AgentId",
        // "ufCrm12AgentEmail",
        // "ufCrm12AgentPhone",
        // "ufCrm12AgentLicense",
        // "ufCrm12AgentPhoto",
        // "ufCrm12Watermark",
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

$filename = "property_" . sanitizeFileName($property['ufCrm12ReferenceNumber']) . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
