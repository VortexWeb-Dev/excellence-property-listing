<div class="w-4/5 mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <form class="w-full space-y-4" id="addPropertyForm" onsubmit="handleAddProperty(event)" enctype="multipart/form-data">
            <!-- Management -->
            <?php include_once('views/components/add-property/management.php'); ?>
            <!-- Specifications -->
            <?php include_once('views/components/add-property/specifications.php'); ?>
            <!-- Property Permit -->
            <?php include_once('views/components/add-property/permit.php'); ?>
            <!-- Pricing -->
            <?php include_once('views/components/add-property/pricing.php'); ?>
            <!-- Title and Description -->
            <?php include_once('views/components/add-property/title.php'); ?>
            <!-- Amenities -->
            <?php include_once('views/components/add-property/amenities.php'); ?>
            <!-- Location -->
            <?php include_once('views/components/add-property/location.php'); ?>
            <!-- Photos and Videos -->
            <?php include_once('views/components/add-property/media.php'); ?>
            <!-- Floor Plan -->
            <?php include_once('views/components/add-property/floorplan.php'); ?>
            <!-- Documents -->
            <?php // include_once('views/components/add-property/documents.php'); 
            ?>
            <!-- Notes -->
            <?php include_once('views/components/add-property/notes.php'); ?>
            <!-- Portals -->
            <?php include_once('views/components/add-property/portals.php'); ?>
            <!-- Status -->
            <?php include_once('views/components/add-property/status.php'); ?>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="javascript:history.back()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1">
                    Back
                </button>
                <button type="submit" id="submitButton" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("offering_type").addEventListener("change", async function() {
        const offeringType = this.value;
        console.log(offeringType);

        if (offeringType == 'RR' || offeringType == 'CR') {
            document.getElementById("rental_period").setAttribute("required", true);
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental) <span class="text-danger">*</span>';
        } else {
            document.getElementById("rental_period").removeAttribute("required");
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental)';
        }

        const newReference = await getNewReference(offeringType);
        document.getElementById("reference").value = newReference;
    })

    async function addItem(entityTypeId, fields) {
        try {
            const response = await fetch(`${API_BASE_URL}crm.item.add?entityTypeId=${entityTypeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fields,
                }),
            });

            if (response.ok) {
                window.location.href = 'index.php?page=properties';
            } else {
                console.error('Failed to add item');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function handleAddProperty(e) {
        e.preventDefault();

        document.getElementById('submitButton').disabled = true;
        document.getElementById('submitButton').innerHTML = 'Submitting...';

        const form = document.getElementById('addPropertyForm');
        const formData = new FormData(form);
        const data = {};

        formData.forEach((value, key) => {
            data[key] = typeof value === 'string' ? value.trim() : value;
        });

        const agent = await getAgent(data.listing_agent);

        const fields = {
            "ufCrm6TitleDeed": data.title_deed,
            "ufCrm6ReferenceNumber": data.reference,
            "ufCrm6OfferingType": data.offering_type,
            "ufCrm6PropertyType": data.property_type,
            "ufCrm6Price": data.price,
            "ufCrm6TitleEn": data.title_en,
            "ufCrm6DescriptionEn": data.description_en,
            "ufCrm6TitleAr": data.title_ar,
            "ufCrm6DescriptionAr": data.description_ar,
            "ufCrm6Size": data.size,
            "ufCrm6Bedroom": data.bedrooms,
            "ufCrm6Bathroom": data.bathrooms,
            "ufCrm6Parking": data.parkings,
            "ufCrm6Geopoints": `${data.latitude}, ${data.longitude}`,
            "ufCrm6PermitNumber": data.dtcm_permit_number,
            "ufCrm6RentalPeriod": data.rental_period,
            "ufCrm6Furnished": data.furnished,
            "ufCrm6TotalPlotSize": data.total_plot_size,
            "ufCrm6LotSize": data.lot_size,
            "ufCrm6BuildupArea": data.buildup_area,
            "ufCrm6LayoutType": data.layout_type,
            "ufCrm6ProjectName": data.project_name,
            "ufCrm6ProjectStatus": data.project_status,
            "ufCrm6Ownership": data.ownership,
            "ufCrm6Developers": data.developer,
            "ufCrm6BuildYear": data.build_year,
            "ufCrm6Availability": data.availability,
            "ufCrm6AvailableFrom": data.available_from,
            "ufCrm6PaymentMethod": data.payment_method,
            "ufCrm6DownPaymentPrice": data.downpayment_price,
            "ufCrm6NoOfCheques": data.cheques,
            "ufCrm6ServiceCharge": data.service_charge,
            "ufCrm6FinancialStatus": data.financial_status,
            "ufCrm6VideoTourUrl": data.video_tour_url,
            "ufCrm_6_360_VIEW_URL": data["360_view_url"],
            "ufCrm6QrCodePropertyBooster": data.qr_code_url,
            "ufCrm6Location": data.pf_location,
            "ufCrm6City": data.pf_city,
            "ufCrm6Community": data.pf_community,
            "ufCrm6SubCommunity": data.pf_subcommunity,
            "ufCrm6Tower": data.pf_building,
            "ufCrm6BayutLocation": data.bayut_location,
            "ufCrm6BayutCity": data.bayut_city,
            "ufCrm6BayutCommunity": data.bayut_community,
            "ufCrm6BayutSubCommunity": data.bayut_subcommunity,
            "ufCrm6BayutTower": data.bayut_building,
            "ufCrm6Status": data.status,
            "ufCrm6ReraPermitNumber": data.rera_permit_number,
            "ufCrm6ReraPermitIssueDate": data.rera_issue_date,
            "ufCrm6ReraPermitExpirationDate": data.rera_expiration_date,
            "ufCrm6DtcmPermitNumber": data.dtcm_permit_number,
            "ufCrm6ListingOwner": data.listing_owner,
            // Landlord 1
            "ufCrm6LandlordName": data.landlord_name,
            "ufCrm6LandlordEmail": data.landlord_email,
            "ufCrm6LandlordContact": data.landlord_phone,
            // Landlord 2
            "ufCrm_12_LANDLORD_NAME_2": data.landlord_name2,
            "ufCrm_12_LANDLORD_EMAIL_2": data.landlord_email2,
            "ufCrm_12_LANDLORD_CONTACT_2": data.landlord_phone2,
            // Landlord 3
            "ufCrm_12_LANDLORD_NAME_3": data.landlord_name3,
            "ufCrm_12_LANDLORD_EMAIL_3": data.landlord_email3,
            "ufCrm_12_LANDLORD_CONTACT_3": data.landlord_phone3,

            "ufCrm6ContractExpiryDate": data.contract_expiry,
            "ufCrm6UnitNo": data.unit_no,
            "ufCrm6SaleType": data.sale_type,
            "ufCrm6BrochureDescription": data.brochure_description_1,
            "ufCrm_12_BROCHURE_DESCRIPTION_2": data.brochure_description_2,
            "ufCrm6HidePrice": data.hide_price == "on" ? "Y" : "N",
            "ufCrm6PfEnable": data.pf_enable == "on" ? "Y" : "N",
            "ufCrm6BayutEnable": data.bayut_enable == "on" ? "Y" : "N",
            "ufCrm6DubizzleEnable": data.dubizzle_enable == "on" ? "Y" : "N",
            "ufCrm6WebsiteEnable": data.website_enable == "on" ? "Y" : "N",
            "ufCrm6Watermark": data.watermark == "on" ? "Y" : "N",
        };

        if (agent) {
            fields["ufCrm6AgentId"] = agent.ufCrm8AgentId;
            fields["ufCrm6AgentName"] = agent.ufCrm8AgentName;
            fields["ufCrm6AgentEmail"] = agent.ufCrm8AgentEmail;
            fields["ufCrm6AgentPhone"] = agent.ufCrm8AgentMobile;
            fields["ufCrm6AgentPhoto"] = agent.ufCrm8AgentPhoto;
            fields["ufCrm6AgentLicense"] = agent.ufCrm8AgentLicense;
        }

        // Notes
        const notesString = data.notes;
        if (notesString) {
            const notesArray = JSON.parse(notesString);
            if (notesArray) {
                fields["ufCrm6Notes"] = notesArray;
            }
        }

        // Amenities
        const amenitiesString = data.amenities;
        if (amenitiesString) {
            const amenitiesArray = JSON.parse(amenitiesString);
            if (amenitiesArray) {
                fields["ufCrm6PrivateAmenities"] = amenitiesArray;
            }
        }

        // Property Photos
        const photos = document.getElementById('selectedImages').value;
        if (photos) {
            const fixedPhotos = photos.replace(/\\'/g, '"');
            const photoArray = JSON.parse(fixedPhotos);
            const watermarkPath = 'assets/images/watermark.png?cache=' + Date.now();
            const uploadedImages = await processBase64Images(photoArray, watermarkPath, data.watermark === "on");

            if (uploadedImages.length > 0) {
                fields["ufCrm6PhotoLinks"] = uploadedImages;
            }
        }

        // Floorplan
        const floorplan = document.getElementById('selectedFloorplan').value;
        if (floorplan) {
            const fixedFloorplan = floorplan.replace(/\\'/g, '"');
            const floorplanArray = JSON.parse(fixedFloorplan);
            const watermarkPath = 'assets/images/watermark.png?cache=' + Date.now();
            const uploadedFloorplan = await processBase64Images(floorplanArray, watermarkPath, data.watermark === "on");

            if (uploadedFloorplan.length > 0) {
                fields["ufCrm6FloorPlan"] = uploadedFloorplan[0];
            }
        }

        // Documents
        // const documents = document.getElementById('documents')?.files;
        // if (documents) {
        //     if (documents.length > 0) {
        //         let documentUrls = [];

        //         for (const document of documents) {
        //             if (document.size > 10485760) {
        //                 alert('File size must be less than 10MB');
        //                 return;
        //             }
        //             const uploadedDocument = await uploadFile(document);
        //             documentUrls.push(uploadedDocument);
        //         }

        //         fields["ufCrm6Documents"] = documentUrls;
        //     }

        // }

        // Add to CRM
        addItem(LISTINGS_ENTITY_TYPE_ID, fields, '?page=properties');
    }

    window.addEventListener('load', async () => {
        const newReference = await getNewReference('RS');
        document.getElementById("reference").value = newReference;
    });
</script>