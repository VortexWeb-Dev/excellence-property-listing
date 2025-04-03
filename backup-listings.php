<?php

require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';
require_once __DIR__ . '/utils/index.php';

class ListingFetcher
{
    private const BATCH_SIZE = 50;
    private array $fieldsToSelect;

    public function __construct()
    {
        $this->fieldsToSelect = [
            "id",
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
            "ufCrm12City",
            "ufCrm12Community",
            "ufCrm12SubCommunity",
            "ufCrm12Tower",
            "ufCrm12BayutLocation",
            "ufCrm12BayutCity",
            "ufCrm12BayutCommunity",
            "ufCrm12BayutSubCommunity",
            "ufCrm12BayutTower",
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
            "ufCrm12AgentId",
            "ufCrm12AgentName",
            "ufCrm12AgentEmail",
            "ufCrm12AgentPhone",
            "ufCrm12AgentLicense",
            "ufCrm12AgentPhoto",
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
            "ufCrm12Watermark",
            "ufCrm_12_LANDLORD_NAME_2",
            "ufCrm_12_LANDLORD_EMAIL_2",
            "ufCrm_12_LANDLORD_CONTACT_2",
            "ufCrm_12_LANDLORD_NAME_3",
            "ufCrm_12_LANDLORD_EMAIL_3",
            "ufCrm_12_LANDLORD_CONTACT_3"
        ];
    }

    public function fetchAll(): array
    {
        try {
            $allListings = [];
            $start = 0;

            do {
                $response = CRest::call('crm.item.list', [
                    'entityTypeId' => LISTINGS_ENTITY_TYPE_ID,
                    'select' => $this->fieldsToSelect,
                    'start' => $start,
                ]);

                if (isset($response['error'])) {
                    error_log("Error fetching listings (start {$start}): " . $response['error_description']);
                    break;
                }

                $items = $response['result']['items'] ?? [];
                $allListings = array_merge($allListings, $items);
                $start += count($items);
            } while (!empty($response['next']) && count($items) === self::BATCH_SIZE);

            return $allListings;
        } catch (Exception $e) {
            error_log("Exception in ListingFetcher::fetchAll: " . $e->getMessage());
            return [];
        }
    }
}

class ListingBackup
{
    private string $backupDir;
    private ListingFetcher $fetcher;

    public function __construct()
    {
        $this->backupDir = __DIR__ . '/backups';
        $this->fetcher = new ListingFetcher();
    }

    private function ensureBackupDirectoryExists(): void
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    private function generateBackupFilename(): string
    {
        return $this->backupDir . '/listings_backup_' . date('Ymd_His') . '.json';
    }

    public function createBackup(): void
    {
        $startTime = microtime(true);

        $listings = $this->fetcher->fetchAll();
        $totalListings = count($listings);
        error_log("Total listings fetched: " . $totalListings);

        if ($totalListings > 0) {
            $this->ensureBackupDirectoryExists();
            $backupFile = $this->generateBackupFilename();

            $jsonData = json_encode($listings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($backupFile, $jsonData) !== false) {
                error_log("Backup saved to: " . $backupFile);
            } else {
                error_log("Failed to save backup file: " . $backupFile);
            }
        } else {
            error_log("No listings found to back up.");
        }

        $executionTime = microtime(true) - $startTime;
        $message = sprintf("Backup process completed in %.3f seconds\n", $executionTime);
        echo $message;
        error_log($message);
    }
}

class BackupManager
{
    public static function run(): void
    {
        try {
            $backup = new ListingBackup();
            $backup->createBackup();
        } catch (Exception $e) {
            error_log("Fatal error in backup process: " . $e->getMessage());
            echo "An error occurred during the backup process. Please check the error logs.\n";
        }
    }
}

BackupManager::run();
