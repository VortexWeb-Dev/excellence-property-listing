<footer class="w-4/5 mx-auto mb-8 px-4 text-center">
    &copy; <?php echo date("Y"); ?> <a href="https://vortexweb.cloud/" target="_blank">VortexWeb</a>
</footer>
<a href="current.php" class="opacity-0 text-xs">invisible link</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./node_modules/lodash/lodash.min.js"></script>
<script src="./node_modules/dropzone/dist/dropzone-min.js"></script>
<script src="./node_modules/preline/dist/preline.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="./node_modules/lodash/lodash.min.js"></script>
<script src="./node_modules/apexcharts/dist/apexcharts.min.js"></script>
<script src="./node_modules/preline/dist/helper-apexcharts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fabric@latest/dist/index.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const isAdmin = localStorage.getItem('isAdmin') == 'true';

        if (!isAdmin) {
            document.querySelectorAll(".admin-only").forEach(el => el.style.display = "none");
        }
    });

    // Toggle Bayut and Dubizzle
    document.getElementById('toggle_bayut_dubizzle') && document.getElementById('toggle_bayut_dubizzle').addEventListener('change', function() {
        const isChecked = this.checked;
        document.getElementById('bayut_enable').checked = isChecked;
        document.getElementById('dubizzle_enable').checked = isChecked;
    });

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Update character count
    function updateCharCount(countElement, length, maxLength) {
        titleCount = document.getElementById(countElement);
        titleCount.textContent = length;

        if (length >= maxLength) {
            titleCount.parentElement.classList.add('text-danger');
        } else {
            titleCount.parentElement.classList.remove('text-danger');
        }
    }

    // Parse and update location fields
    function updateLocationFields(location, type) {
        const locationParts = location.split('-');

        const city = locationParts[0].trim();
        const community = locationParts[1].trim();
        const subcommunity = locationParts[2].trim() || null;
        const building = locationParts[3].trim() || null;

        document.getElementById(`${type}_city`).value = city;
        document.getElementById(`${type}_community`).value = community;
        document.getElementById(`${type}_subcommunity`).value = subcommunity;
        document.getElementById(`${type}_building`).value = building;
    }

    // Update reference
    async function handleUpdateReference(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const propertyId = formData.get('propertyId');
        const newReference = formData.get('newReference');

        try {
            const response = await fetch(`${API_BASE_URL}crm.item.update?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}&fields[ufCrm6ReferenceNumber]=${newReference}`);
            const data = await response.json();
            location.reload();
        } catch (error) {
            console.error('Error updating reference:', error);
        }
    }

    // Format input date
    function formatInputDate(dateInput) {
        if (!dateInput) return null;

        const date = new Date(dateInput);

        if (isNaN(date.getTime())) return null;

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    // Get agent
    async function getAgent(agentId) {
        const response = await fetch(`${API_BASE_URL}crm.item.list?entityTypeId=${AGENTS_ENTITY_ID}&filter[ufCrm8AgentId]=${agentId}`);
        const data = await response.json();
        return data.result.items[0] || null;
    }

    // Handle action
    async function handleAction(action, propertyId, platform = null) {
        const baseUrl = API_BASE_URL;
        let apiUrl = '';
        let reloadRequired = true;

        switch (action) {
            case 'copyLink':
                const link = `https://lightgray-kudu-834713.hostingersite.com/property-listing-gi/index.php?page=view-property&id=${propertyId}`;
                navigator.clipboard.writeText(link);
                alert('Link copied to clipboard.');
                reloadRequired = false;
                break;

            case 'downloadPDF':
                window.location.href = `download-pdf.php?id=${propertyId}`;
                reloadRequired = false;
                break;
            case 'downloadPDFAgent':
                window.location.href = `download-pdf-agent.php?id=${propertyId}`;
                reloadRequired = false;
                break;
            case 'export-excel':
                window.location.href = `export-excel.php?id=${propertyId}`;
                reloadRequired = false;
                break;

            case 'duplicate':
                try {
                    const getUrl = `${baseUrl}/crm.item.get?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}&select[0]=id&select[1]=uf_*`;
                    const response = await fetch(getUrl, {
                        method: 'GET'
                    });
                    const data = await response.json();
                    const property = data.result.item;

                    let addUrl = `${baseUrl}/crm.item.add?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}`;
                    for (const field in property) {
                        if (
                            field.startsWith('ufCrm6') &&
                            !['ufCrm6ReferenceNumber', 'ufCrm6TitleEn', 'ufCrm6Status', 'ufCrm6PhotoLinks', 'ufCrm6Documents', 'ufCrm6Notes'].includes(field)
                        ) {
                            addUrl += `&fields[${field}]=${encodeURIComponent(property[field])}`;
                        }
                    }

                    if (property['ufCrm6PhotoLinks']) {
                        property['ufCrm6PhotoLinks'].forEach((photoLink, index) => {
                            addUrl += `&fields[ufCrm6PhotoLinks][${index}]=${encodeURIComponent(photoLink)}`;
                        });
                    }

                    if (property['ufCrm6Documents']) {
                        property['ufCrm6Documents'].forEach((document, index) => {
                            addUrl += `&fields[ufCrm6Documents][${index}]=${encodeURIComponent(document)}`;
                        });
                    }

                    if (property['ufCrm6Notes']) {
                        property['ufCrm6Notes'].forEach((note, index) => {
                            addUrl += `&fields[ufCrm6Notes][${index}]=${encodeURIComponent(note)}`;
                        });
                    }

                    addUrl += `&fields[ufCrm6TitleEn]=${encodeURIComponent(property.ufCrm6TitleEn + ' (Duplicate)')}`;
                    addUrl += `&fields[ufCrm6ReferenceNumber]=${await getNewReference(property.ufCrm6OfferingType)}`;
                    addUrl += `&fields[ufCrm6Status]=DRAFT`;

                    await fetch(addUrl, {
                        method: 'GET'
                    });
                } catch (error) {
                    console.error('Error duplicating property:', error);
                }
                break;

            case 'publish':
                apiUrl = `${baseUrl}/crm.item.update?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}&fields[ufCrm6Status]=PUBLISHED`;
                if (platform) {
                    apiUrl += `&fields[ufCrm6${platform.charAt(0).toUpperCase() + platform.slice(1)}Enable]=Y`;
                } else {
                    apiUrl += `&fields[ufCrm6PfEnable]=Y&fields[ufCrm6BayutEnable]=Y&fields[ufCrm6DubizzleEnable]=Y&fields[ufCrm6WebsiteEnable]=Y&fields[ufCrm6Status]=PUBLISHED`;
                }
                break;

            case 'unpublish':
                apiUrl = `${baseUrl}/crm.item.update?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}`;
                if (platform) {
                    apiUrl += `&fields[ufCrm6${platform.charAt(0).toUpperCase() + platform.slice(1)}Enable]=N`;
                } else {
                    apiUrl += `&fields[ufCrm6PfEnable]=N&fields[ufCrm6BayutEnable]=N&fields[ufCrm6DubizzleEnable]=N&fields[ufCrm6WebsiteEnable]=N&fields[ufCrm6Status]=UNPUBLISHED`;
                }
                break;

            case 'archive':
                if (confirm('Are you sure you want to archive this property?')) {
                    apiUrl = `${baseUrl}/crm.item.update?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}&fields[ufCrm6Status]=ARCHIVED`;
                } else {
                    reloadRequired = false;
                }
                break;

            case 'delete':
                if (confirm('Are you sure you want to delete this property?')) {
                    try {
                        // First get property details to find image URLs
                        const getPropertyUrl = `${baseUrl}/crm.item.get?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}`;
                        const propertyResponse = await fetch(getPropertyUrl);
                        const propertyData = await propertyResponse.json();

                        if (propertyData.result && propertyData.result.item) {
                            const property = propertyData.result.item;
                            console.log('Property data for deletion:', property);

                            // Delete images from S3
                            if (property.ufCrm6PhotoLinks && Array.isArray(property.ufCrm6PhotoLinks)) {
                                console.log('Found photo links:', property.ufCrm6PhotoLinks);
                                for (const imageUrl of property.ufCrm6PhotoLinks) {
                                    try {
                                        console.log('Attempting to delete image:', imageUrl);
                                        const response = await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: imageUrl
                                            })
                                        });
                                        const result = await response.json();
                                        console.log('Delete response:', result);
                                        if (!result.success) {
                                            console.error(`Failed to delete image: ${result.error}`);
                                        }
                                    } catch (error) {
                                        console.error(`Error deleting S3 object: ${imageUrl}`, error);
                                    }
                                }
                            }

                            // Delete floorplan from S3 if exists
                            if (property.ufCrm6FloorPlan) {
                                try {
                                    console.log('Attempting to delete floorplan:', property.ufCrm6FloorPlan);
                                    const response = await fetch('./delete-s3-object.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            fileUrl: property.ufCrm6FloorPlan
                                        })
                                    });
                                    const result = await response.json();
                                    console.log('Floorplan delete response:', result);
                                    if (!result.success) {
                                        console.error(`Failed to delete floorplan: ${result.error}`);
                                    }
                                } catch (error) {
                                    console.error(`Error deleting S3 floorplan: ${property.ufCrm6FloorPlan}`, error);
                                }
                            }

                            // Delete documents from S3
                            if (property.ufCrm6Documents && Array.isArray(property.ufCrm6Documents)) {
                                console.log('Found documents:', property.ufCrm6Documents);
                                for (const docUrl of property.ufCrm6Documents) {
                                    try {
                                        console.log('Attempting to delete document:', docUrl);
                                        const response = await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: docUrl
                                            })
                                        });
                                        const result = await response.json();
                                        console.log('Delete response:', result);
                                        if (!result.success) {
                                            console.error(`Failed to delete document: ${result.error}`);
                                        }
                                    } catch (error) {
                                        console.error(`Error deleting S3 document: ${docUrl}`, error);
                                    }
                                }
                            }
                        }

                        // Now delete the property from CRM
                        apiUrl = `${baseUrl}/crm.item.delete?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}`;
                    } catch (error) {
                        console.error('Error in delete process:', error);
                        reloadRequired = false;
                    }
                } else {
                    reloadRequired = false;
                }
                break;

            default:
                console.error('Invalid action:', action);
                reloadRequired = false;
        }

        if (apiUrl) {
            try {
                await fetch(apiUrl, {
                    method: 'GET'
                });
            } catch (error) {
                console.error(`Error executing ${action}:`, error);
            }
        }

        if (reloadRequired) {
            location.reload();
        }
    }

    // Bulk action
    async function handleBulkAction(action, platform) {
        const checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        const propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (propertyIds.length === 0) {
            alert('Please select at least one property.');
            return;
        }

        if (confirm(`Are you sure you want to ${action} the selected properties?`)) {
            try {
                const baseUrl = API_BASE_URL;
                const apiUrl = `${baseUrl}/crm.item.${action === 'delete' ? 'delete' : 'update'}?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}`;

                const platformFieldMapping = {
                    pf: 'ufCrm6PfEnable',
                    bayut: 'ufCrm6BayutEnable',
                    dubizzle: 'ufCrm6DubizzleEnable',
                    website: 'ufCrm6WebsiteEnable'
                };

                // If action is delete, first get all property details to find image URLs
                if (action === 'delete') {
                    for (const propertyId of propertyIds) {
                        try {
                            // Get property details to find image URLs
                            const getPropertyUrl = `${baseUrl}/crm.item.get?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${propertyId}`;
                            const propertyResponse = await fetch(getPropertyUrl);
                            const propertyData = await propertyResponse.json();
                            console.log('Property data:', propertyData);
                            if (propertyData.result && propertyData.result.item) {
                                const property = propertyData.result.item;

                                // Delete images from S3
                                if (property.ufCrm6PhotoLinks && Array.isArray(property.ufCrm6PhotoLinks)) {
                                    for (const imageUrl of property.ufCrm6PhotoLinks) {
                                        try {
                                            await fetch('./delete-s3-object.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                    fileUrl: imageUrl
                                                })
                                            });
                                        } catch (error) {
                                            console.error(`Error deleting S3 object: ${imageUrl}`, error);
                                        }
                                    }
                                }

                                // Delete floorplan from S3 if exists
                                if (property.ufCrm6FloorPlan) {
                                    try {
                                        await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: property.ufCrm6FloorPlan
                                            })
                                        });
                                    } catch (error) {
                                        console.error(`Error deleting S3 floorplan: ${property.ufCrm6FloorPlan}`, error);
                                    }
                                }

                                // Delete documents from S3
                                if (property.ufCrm6Documents && Array.isArray(property.ufCrm6Documents)) {
                                    for (const docUrl of property.ufCrm6Documents) {
                                        try {
                                            await fetch('./delete-s3-object.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                    fileUrl: docUrl
                                                })
                                            });
                                        } catch (error) {
                                            console.error(`Error deleting S3 document: ${docUrl}`, error);
                                        }
                                    }
                                }
                            }
                        } catch (error) {
                            console.error(`Error getting property details for deletion: ${propertyId}`, error);
                        }
                    }
                }

                const requests = propertyIds.map(propertyId => {
                    let url = `${apiUrl}&id=${propertyId}`;

                    if (action === 'publish') {
                        url += '&fields[ufCrm6Status]=PUBLISHED';

                        if (platformFieldMapping[platform]) {
                            url += `&fields[${platformFieldMapping[platform]}]=Y`;
                        } else {
                            url += `&fields[ufCrm6PfEnable]=Y&fields[ufCrm6BayutEnable]=Y&fields[ufCrm6DubizzleEnable]=Y&fields[ufCrm6WebsiteEnable]=Y`;
                        }
                    } else if (action === 'unpublish') {
                        if (platformFieldMapping[platform]) {
                            url += `&fields[${platformFieldMapping[platform]}]=N`;
                        } else {
                            url += `&fields[ufCrm6PfEnable]=N&fields[ufCrm6BayutEnable]=N&fields[ufCrm6DubizzleEnable]=N&fields[ufCrm6WebsiteEnable]=N&fields[ufCrm6Status]=UNPUBLISHED`;
                        }
                    } else if (action === 'archive') {
                        url += '&fields[ufCrm6Status]=ARCHIVED';
                    }

                    return fetch(url, {
                            method: 'GET'
                        })
                        .then(response => response.json())
                        .then(data => {})
                        .catch(error => {
                            console.error(`Error updating property ${propertyId}:`, error);
                        });
                });

                // Wait for all requests to finish
                await Promise.all(requests);

                location.reload();
            } catch (error) {
                console.error('Error handling bulk action:', error);
            }
        }
    }

    // Function to add watermark to the image
    function addWatermark(imageElement, watermarkImagePath) {
        return new Promise((resolve, reject) => {
            const watermarkImage = new Image();
            watermarkImage.src = watermarkImagePath;

            watermarkImage.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const width = imageElement.width;
                const height = imageElement.height;

                canvas.width = width;
                canvas.height = height;

                // Draw original image
                ctx.drawImage(imageElement, 0, 0, width, height);

                // Draw watermark image over it without resizing or positioning
                ctx.drawImage(watermarkImage, 0, 0, width, height);

                const watermarkedImage = canvas.toDataURL('image/jpeg', 0.9);
                resolve(watermarkedImage);
            };

            watermarkImage.onerror = function() {
                reject('Failed to load watermark image.');
            };
        });
    }


    // Function to add watermark text to the image
    function addWatermarkText(imageElement, watermarkText) {
        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const width = imageElement.width;
            const height = imageElement.height;

            canvas.width = width;
            canvas.height = height;

            ctx.drawImage(imageElement, 0, 0, width, height);

            // Set the watermark text properties
            ctx.font = '360px Arial'; // You can adjust the font size here
            ctx.fillStyle = 'rgba(255, 255, 255, 0.6)'; // White color with 50% transparency
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Add the watermark text to the image (centered)
            ctx.fillText(watermarkText, width / 2, height / 2);

            // Convert the image to JPEG with reduced quality (optional)
            const watermarkedImage = canvas.toDataURL('image/jpeg', 0.7); // Adjust quality as needed
            resolve(watermarkedImage);
        });
    }

    // Function to upload a file
    function uploadFile(file, isDocument = false) {
        const formData = new FormData();
        formData.append('file', file);

        if (isDocument) {
            formData.append('isDocument', 'true');
        }

        return fetch('upload-file.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    return data.url;
                } else {
                    console.error('Error uploading file (PHP backend):', data.error);
                    return null;
                }
            })
            .catch((error) => {
                console.error("Error uploading file:", error);
                return null;
            });
    }

    // Process base64 images
    async function processBase64Images(base64Images, watermarkPath, watermark = true) {
        const photoPaths = [];
        const TARGET_ASPECT_RATIO = 4 / 3;

        function resizeToAspectRatio(image) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            let newWidth = image.width;
            let newHeight = image.height;
            const currentAspectRatio = image.width / image.height;

            if (currentAspectRatio > TARGET_ASPECT_RATIO) {
                newWidth = image.height * TARGET_ASPECT_RATIO;
                newHeight = image.height;
            } else if (currentAspectRatio < TARGET_ASPECT_RATIO) {
                newWidth = image.width;
                newHeight = image.width / TARGET_ASPECT_RATIO;
            }

            canvas.width = newWidth;
            canvas.height = newHeight;

            const xOffset = (newWidth - image.width) / 2;
            const yOffset = (newHeight - image.height) / 2;

            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, newWidth, newHeight);

            ctx.drawImage(
                image,
                xOffset,
                yOffset,
                image.width,
                image.height
            );

            return canvas.toDataURL();
        }

        for (const base64Image of base64Images) {
            const regex = /^data:image\/(\w+);base64,/;
            const matches = base64Image.match(regex);

            if (matches) {
                const base64Data = base64Image.replace(regex, '');
                const imageData = atob(base64Data);

                const blob = new Blob([new Uint8Array(imageData.split('').map(c => c.charCodeAt(0)))], {
                    type: `image/${matches[1]}`,
                });
                const imageUrl = URL.createObjectURL(blob);

                const imageElement = new Image();
                imageElement.src = imageUrl;

                await new Promise((resolve, reject) => {
                    imageElement.onload = async () => {
                        try {
                            const resizedDataUrl = resizeToAspectRatio(imageElement);
                            const resizedImage = new Image();
                            resizedImage.src = resizedDataUrl;

                            await new Promise(resolve => {
                                resizedImage.onload = resolve;
                            });

                            const finalDataUrl = watermark ?
                                await addWatermark(resizedImage, watermarkPath) :
                                resizedDataUrl;

                            const finalBlob = dataURLToBlob(finalDataUrl);
                            const uploadedUrl = await uploadFile(finalBlob);

                            if (uploadedUrl) {
                                photoPaths.push(uploadedUrl);
                            } else {
                                console.error('Error uploading photo from base64 data');
                            }

                            resolve();
                        } catch (error) {
                            console.error('Error processing watermarking or uploading:', error);
                            reject(error);
                        } finally {
                            URL.revokeObjectURL(imageUrl);
                        }
                    };

                    imageElement.onerror = (error) => {
                        console.error('Failed to load image from URL:', error);
                        reject(error);
                    };
                });
            } else {
                console.error('Invalid base64 image data');
            }
        }

        return photoPaths;
    }

    function getAmenityName(amenityId) {
        const amenities = [{
                id: 'GV',
                label: 'Golf view'
            },
            {
                id: 'CW',
                label: 'City view'
            },
            {
                id: 'NO',
                label: 'North orientation'
            },
            {
                id: 'SO',
                label: 'South orientation'
            },
            {
                id: 'EO',
                label: 'East orientation'
            },
            {
                id: 'WO',
                label: 'West orientation'
            },
            {
                id: 'NS',
                label: 'Near school'
            },
            {
                id: 'HO',
                label: 'Near hospital'
            },
            {
                id: 'TR',
                label: 'Terrace'
            },
            {
                id: 'NM',
                label: 'Near mosque'
            },
            {
                id: 'SM',
                label: 'Near supermarket'
            },
            {
                id: 'ML',
                label: 'Near mall'
            },
            {
                id: 'PT',
                label: 'Near public transportation'
            },
            {
                id: 'MO',
                label: 'Near metro'
            },
            {
                id: 'VT',
                label: 'Near veterinary'
            },
            {
                id: 'BC',
                label: 'Beach access'
            },
            {
                id: 'PK',
                label: 'Public parks'
            },
            {
                id: 'RT',
                label: 'Near restaurants'
            },
            {
                id: 'NG',
                label: 'Near Golf'
            },
            {
                id: 'AP',
                label: 'Near airport'
            },
            {
                id: 'CS',
                label: 'Concierge Service'
            },
            {
                id: 'SS',
                label: 'Spa'
            },
            {
                id: 'SY',
                label: 'Shared Gym'
            },
            {
                id: 'MS',
                label: 'Maid Service'
            },
            {
                id: 'WC',
                label: 'Walk-in Closet'
            },
            {
                id: 'HT',
                label: 'Heating'
            },
            {
                id: 'GF',
                label: 'Ground floor'
            },
            {
                id: 'SV',
                label: 'Server room'
            },
            {
                id: 'DN',
                label: 'Pantry'
            },
            {
                id: 'RA',
                label: 'Reception area'
            },
            {
                id: 'VP',
                label: 'Visitors parking'
            },
            {
                id: 'OP',
                label: 'Office partitions'
            },
            {
                id: 'SH',
                label: 'Core and Shell'
            },
            {
                id: 'CD',
                label: 'Children daycare'
            },
            {
                id: 'CL',
                label: 'Cleaning services'
            },
            {
                id: 'NH',
                label: 'Near Hotel'
            },
            {
                id: 'CR',
                label: 'Conference room'
            },
            {
                id: 'BL',
                label: 'View of Landmark'
            },
            {
                id: 'PR',
                label: 'Children Play Area'
            },
            {
                id: 'BH',
                label: 'Beach Access'
            },
            {
                id: 'AC',
                label: 'Central A/C & Heating'
            },
            {
                id: 'BA',
                label: 'Balcony'
            },
            {
                id: 'BK',
                label: 'Built-in Kitchen Appliances'
            },
            {
                id: 'BW',
                label: 'Built-in Wardrobes'
            },
            {
                id: 'CP',
                label: 'Covered Parking'
            },
            {
                id: 'LB',
                label: 'Lobby in Building'
            },
            {
                id: 'MR',
                label: "Maid's Room"
            },
            {
                id: 'PA',
                label: 'Pets Allowed'
            },
            {
                id: 'PG',
                label: 'Private Garden'
            },
            {
                id: 'PJ',
                label: 'Private Jacuzzi'
            },
            {
                id: 'PP',
                label: 'Private Pool'
            },
            {
                id: 'PY',
                label: 'Private Gym'
            },
            {
                id: 'VC',
                label: 'Vastu-compliant'
            },
            {
                id: 'SE',
                label: 'Security'
            },
            {
                id: 'SP',
                label: 'Shared Pool'
            },
            {
                id: 'ST',
                label: 'Study'
            },
            {
                id: 'VW',
                label: 'View of Water'
            },
            {
                id: 'CO',
                label: "Children's Pool"
            },
            {
                id: 'BR',
                label: 'Barbecue Area'
            }
        ];

        return amenities.find(amenity => amenity.id === amenityId)?.label || amenityId;
    }

    function getAmenityId(amenityName) {
        const amenities = [{
                id: 'GV',
                label: 'Golf view'
            },
            {
                id: 'CW',
                label: 'City view'
            },
            {
                id: 'NO',
                label: 'North orientation'
            },
            {
                id: 'SO',
                label: 'South orientation'
            },
            {
                id: 'EO',
                label: 'East orientation'
            },
            {
                id: 'WO',
                label: 'West orientation'
            },
            {
                id: 'NS',
                label: 'Near school'
            },
            {
                id: 'HO',
                label: 'Near hospital'
            },
            {
                id: 'TR',
                label: 'Terrace'
            },
            {
                id: 'NM',
                label: 'Near mosque'
            },
            {
                id: 'SM',
                label: 'Near supermarket'
            },
            {
                id: 'ML',
                label: 'Near mall'
            },
            {
                id: 'PT',
                label: 'Near public transportation'
            },
            {
                id: 'MO',
                label: 'Near metro'
            },
            {
                id: 'VT',
                label: 'Near veterinary'
            },
            {
                id: 'BC',
                label: 'Beach access'
            },
            {
                id: 'PK',
                label: 'Public parks'
            },
            {
                id: 'RT',
                label: 'Near restaurants'
            },
            {
                id: 'NG',
                label: 'Near Golf'
            },
            {
                id: 'AP',
                label: 'Near airport'
            },
            {
                id: 'CS',
                label: 'Concierge Service'
            },
            {
                id: 'SS',
                label: 'Spa'
            },
            {
                id: 'SY',
                label: 'Shared Gym'
            },
            {
                id: 'MS',
                label: 'Maid Service'
            },
            {
                id: 'WC',
                label: 'Walk-in Closet'
            },
            {
                id: 'HT',
                label: 'Heating'
            },
            {
                id: 'GF',
                label: 'Ground floor'
            },
            {
                id: 'SV',
                label: 'Server room'
            },
            {
                id: 'DN',
                label: 'Pantry'
            },
            {
                id: 'RA',
                label: 'Reception area'
            },
            {
                id: 'VP',
                label: 'Visitors parking'
            },
            {
                id: 'OP',
                label: 'Office partitions'
            },
            {
                id: 'SH',
                label: 'Core and Shell'
            },
            {
                id: 'CD',
                label: 'Children daycare'
            },
            {
                id: 'CL',
                label: 'Cleaning services'
            },
            {
                id: 'NH',
                label: 'Near Hotel'
            },
            {
                id: 'CR',
                label: 'Conference room'
            },
            {
                id: 'BL',
                label: 'View of Landmark'
            },
            {
                id: 'PR',
                label: 'Children Play Area'
            },
            {
                id: 'BH',
                label: 'Beach Access'
            },
            {
                id: 'AC',
                label: 'Central A/C & Heating'
            },
            {
                id: 'BA',
                label: 'Balcony'
            },
            {
                id: 'BK',
                label: 'Built-in Kitchen Appliances'
            },
            {
                id: 'BW',
                label: 'Built-in Wardrobes'
            },
            {
                id: 'CP',
                label: 'Covered Parking'
            },
            {
                id: 'LB',
                label: 'Lobby in Building'
            },
            {
                id: 'MR',
                label: "Maid's Room"
            },
            {
                id: 'PA',
                label: 'Pets Allowed'
            },
            {
                id: 'PG',
                label: 'Private Garden'
            },
            {
                id: 'PJ',
                label: 'Private Jacuzzi'
            },
            {
                id: 'PP',
                label: 'Private Pool'
            },
            {
                id: 'PY',
                label: 'Private Gym'
            },
            {
                id: 'VC',
                label: 'Vastu-compliant'
            },
            {
                id: 'SE',
                label: 'Security'
            },
            {
                id: 'SP',
                label: 'Shared Pool'
            },
            {
                id: 'ST',
                label: 'Study'
            },
            {
                id: 'VW',
                label: 'View of Water'
            },
            {
                id: 'CO',
                label: "Children's Pool"
            },
            {
                id: 'BR',
                label: 'Barbecue Area'
            }
        ];

        return amenities.find(amenity => amenity.label === amenityName)?.id || amenityName;
    }

    // Function to convert data URL to Blob
    function dataURLToBlob(dataURL) {
        const byteString = atob(dataURL.split(',')[1]);
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const uintArray = new Uint8Array(arrayBuffer);
        for (let i = 0; i < byteString.length; i++) {
            uintArray[i] = byteString.charCodeAt(i);
        }
        return new Blob([uintArray], {
            type: 'image/png'
        });
    }

    // Function to fetch a property
    async function fetchProperty(id) {
        const url = `${API_BASE_URL}crm.item.get?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&id=${id}`;
        console.log("url: ", url)
        const response = await fetch(url);
        const data = await response.json();
        console.log("data: ", data)
        console.log("data.result: ", data.result)
        console.log("data.result.item: ", data.result.item);

        if (data.result && data.result.item) {
            const property = data.result.item;

            // Management
            document.getElementById('reference').value = property.ufCrm6ReferenceNumber;

            // Landlord 1
            document.getElementById('landlord_name').value = property.ufCrm6LandlordName;
            document.getElementById('landlord_email').value = property.ufCrm6LandlordEmail;
            document.getElementById('landlord_phone').value = property.ufCrm6LandlordContact;
            // // Landlord 2
            // document.getElementById('landlord_name2').value = property.ufCrm_12_LANDLORD_NAME_2;
            // document.getElementById('landlord_email2').value = property.ufCrm_12_LANDLORD_EMAIL_2;
            // document.getElementById('landlord_phone2').value = property.ufCrm_12_LANDLORD_CONTACT_2;
            // // Landlord 3
            // document.getElementById('landlord_name3').value = property.ufCrm_12_LANDLORD_NAME_3;
            // document.getElementById('landlord_email3').value = property.ufCrm_12_LANDLORD_EMAIL_3;
            // document.getElementById('landlord_phone3').value = property.ufCrm_12_LANDLORD_CONTACT_3;

            Array.from(document.getElementById('availability').options).forEach(option => {
                if (option.value == property.ufCrm6Availability) option.selected = true;
            });
            document.getElementById('available_from').value = formatInputDate(property.ufCrm6AvailableFrom);
            document.getElementById('contract_expiry').value = formatInputDate(property.ufCrm6ContractExpiryDate);

            // Specifications
            document.getElementById('title_deed').value = property.ufCrm6TitleDeed;
            document.getElementById('size').value = property.ufCrm6Size;
            document.getElementById('unit_no').value = property.ufCrm6UnitNo;
            document.getElementById('bathrooms').value = property.ufCrm6Bathroom;
            document.getElementById('parkings').value = property.ufCrm6Parking;
            document.getElementById('total_plot_size').value = property.ufCrm6TotalPlotSize;
            document.getElementById('lot_size').value = property.ufCrm6LotSize;
            document.getElementById('buildup_area').value = property.ufCrm6BuildupArea;
            document.getElementById('layout_type').value = property.ufCrm6LayoutType;
            document.getElementById('project_name').value = property.ufCrm6ProjectName;
            document.getElementById('build_year').value = property.ufCrm6BuildYear;
            Array.from(document.getElementById('property_type').options).forEach(option => {
                if (option.value === property.ufCrm6PropertyType) option.selected = true;
            });
            Array.from(document.getElementById('offering_type').options).forEach(option => {
                if (option.value === property.ufCrm6OfferingType) option.selected = true;
            });
            Array.from(document.getElementById('bedrooms').options).forEach(option => {
                if (option.value == property.ufCrm6Bedroom) option.selected = true;
            });
            Array.from(document.getElementById('furnished').options).forEach(option => {
                if (option.value == property.ufCrm6Furnished) option.selected = true;
            });
            Array.from(document.getElementById('project_status').options).forEach(option => {
                if (option.value == property.ufCrm6ProjectStatus) option.selected = true;
            });
            Array.from(document.getElementById('sale_type').options).forEach(option => {
                if (option.value == property.ufCrm6SaleType) option.selected = true;
            });
            Array.from(document.getElementById('ownership').options).forEach(option => {
                if (option.value == property.ufCrm6Ownership) option.selected = true;
            });

            // Property Permit
            document.getElementById('rera_permit_number').value = property.ufCrm6ReraPermitNumber
            document.getElementById('dtcm_permit_number').value = property.ufCrm6DtcmPermitNumber
            document.getElementById('rera_issue_date').value = formatInputDate(property.ufCrm6ReraPermitIssueDate);
            document.getElementById('rera_expiration_date').value = formatInputDate(property.ufCrm6ReraPermitExpirationDate);

            // Pricing
            document.getElementById('price').value = property.ufCrm6Price;
            document.getElementById('payment_method').value = property.ufCrm6PaymentMethod;
            document.getElementById('downpayment_price').value = property.ufCrm6DownPaymentPrice;
            document.getElementById('service_charge').value = property.ufCrm6ServiceCharge;
            property.ufCrm6HidePrice == "Y" ? document.getElementById('hide_price').checked = true : document.getElementById('hide_price').checked = false;
            Array.from(document.getElementById('rental_period').options).forEach(option => {
                if (option.value == property.ufCrm6RentalPeriod) option.selected = true;
            });
            Array.from(document.getElementById('cheques').options).forEach(option => {
                if (option.value == property.ufCrm6NoOfCheques) option.selected = true;
            });
            Array.from(document.getElementById('financial_status').options).forEach(option => {
                if (option.value == property.ufCrm6FinancialStatus) option.selected = true;
            });

            // Title and Description
            document.getElementById('title_en').value = property.ufCrm6TitleEn;
            document.getElementById('description_en').textContent = property.ufCrm6DescriptionEn;
            document.getElementById('title_ar').value = property.ufCrm6TitleAr;
            document.getElementById('description_ar').textContent = property.ufCrm6DescriptionAr;
            document.getElementById('brochure_description_1').textContent = property.ufCrm6BrochureDescription;
            document.getElementById('brochure_description_2').textContent = property.ufCrm_12_BROCHURE_DESCRIPTION_2;

            document.getElementById('titleEnCount').textContent = document.getElementById('title_en').value.length;
            document.getElementById('descriptionEnCount').textContent = document.getElementById('description_en').textContent.length;
            document.getElementById('titleArCount').textContent = document.getElementById('title_ar').value.length;
            document.getElementById('descriptionArCount').textContent = document.getElementById('description_ar').textContent.length;
            document.getElementById('brochureDescription1Count').textContent = document.getElementById('brochure_description_1').textContent.length;
            document.getElementById('brochureDescription2Count').textContent = document.getElementById('brochure_description_2').textContent.length;

            // Location
            document.getElementById('pf_location').value = property.ufCrm6Location;
            document.getElementById('pf_city').value = property.ufCrm6City;
            document.getElementById('pf_community').value = property.ufCrm6Community;
            document.getElementById('pf_subcommunity').value = property.ufCrm6SubCommunity;
            document.getElementById('pf_building').value = property.ufCrm6Tower;
            document.getElementById('bayut_location').value = property.ufCrm6BayutLocation;
            document.getElementById('bayut_city').value = property.ufCrm6BayutCity;
            document.getElementById('bayut_community').value = property.ufCrm6BayutCommunity;
            document.getElementById('bayut_subcommunity').value = property.ufCrm6BayutSubCommunity;
            document.getElementById('bayut_building').value = property.ufCrm6BayutTower;

            if (property.ufCrm6Geopoints) {
                const [latitude, longitude] = property.ufCrm6Geopoints.split(',').map(coord => coord.trim());
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;
            }

            // Photos and Videos
            document.getElementById('video_tour_url').value = property.ufCrm6VideoTourUrl;
            document.getElementById('360_view_url').value = property.ufCrm_6_360_VIEW_URL;
            document.getElementById('qr_code_url').value = property.ufCrm6QrCodePropertyBooster;
            // Photos
            // Floor Plan

            // Portals
            property.ufCrm6PfEnable == "Y" ? document.getElementById('pf_enable').checked = true : document.getElementById('pf_enable').checked = false;
            property.ufCrm6BayutEnable == "Y" ? document.getElementById('bayut_enable').checked = true : document.getElementById('bayut_enable').checked = false;
            property.ufCrm6DubizzleEnable == "Y" ? document.getElementById('dubizzle_enable').checked = true : document.getElementById('dubizzle_enable').checked = false;
            property.ufCrm6WebsiteEnable == "Y" ? document.getElementById('website_enable').checked = true : document.getElementById('website_enable').checked = false;
            property.ufCrm6Watermark == "Y" ? document.getElementById('watermark').checked = true : document.getElementById('watermark').checked = false;
            if (document.getElementById('dubizzle_enable').checked && document.getElementById('bayut_enable').value) {
                toggle_bayut_dubizzle.checked = true;
            }

            switch (property.ufCrm6Status) {
                case 'PUBLISHED':
                    document.getElementById('publish').checked = true;
                    break;
                case 'UNPUBLISHED':
                    document.getElementById('unpublish').checked = true;
                    break;
                case 'LIVE':
                    document.getElementById('live').checked = true;
                    break;
                case 'DRAFT':
                    document.getElementById('draft').checked = true;
                    break;
                case 'ARCHIVED':
                    document.getElementById('archive').checked = true;
                    break;
                case 'POCKET':
                    document.getElementById('pocket').checked = true;
                    break;
            }

            function ensureOptionExistsAndSelect(selectElementId, value, label) {
                const selectElement = document.getElementById(selectElementId);
                const existingOption = document.querySelector(`#${selectElementId} option[value="${value}"]`);

                if (!existingOption) {
                    const newOption = document.createElement('option');
                    newOption.value = value;
                    newOption.textContent = label || 'Unknown Option';
                    newOption.selected = true;
                    selectElement.appendChild(newOption);
                } else {
                    existingOption.selected = true;
                }
            }

            ensureOptionExistsAndSelect('listing_agent', property.ufCrm6AgentId, property.ufCrm6AgentName);
            ensureOptionExistsAndSelect('listing_owner', property.ufCrm6ListingOwner, property.ufCrm6ListingOwner);
            ensureOptionExistsAndSelect('developer', property.ufCrm6Developers, property.ufCrm6Developers);

            // Notes
            function addExistingNote(note) {
                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md");

                li.innerHTML = `
                    ${note} 
                    <button class="text-red-500 hover:text-red-700" onclick="removeNote(this)">×</button>
                `;

                document.getElementById("notesList").appendChild(li);
                updateNotesInput();
            }

            if (property.ufCrm6Notes.length > 0) {
                property.ufCrm6Notes.forEach(note => {
                    addExistingNote(note);
                });
            }

            // Amenities
            function addExistingAmenity(amenity) {
                if (!selectedAmenities.some(a => a.id === amenity)) {
                    selectedAmenities.push({
                        id: amenity,
                        label: getAmenityName(amenity)
                    });
                }

                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md");

                li.innerHTML = `
                    ${getAmenityName(amenity)} 
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeAmenity('${amenity}')">×</button>
                `;

                document.getElementById("selectedAmenities").appendChild(li);
                updateAmenitiesInput();
            }

            if (property.ufCrm6PrivateAmenities && property.ufCrm6PrivateAmenities.length > 0) {
                property.ufCrm6PrivateAmenities.forEach(amenity => {
                    addExistingAmenity(amenity);
                });
            }


            return property;

        } else {
            console.error('Invalid property data:', data);
            document.getElementById('property-details').textContent = 'Failed to load property details.';
        }
    }

    // Function to check if any property is selected
    function isPropertySelected() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        return propertyIds && propertyIds.length > 0;
    }

    // Function to select and add properties to agent transfer form
    function selectAndAddPropertiesToAgentTransfer() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (!isPropertySelected()) {
            return alert('Please select at least one property.');
        }

        document.getElementById('transferAgentPropertyIds').value = propertyIds.join(',');

        const agentModal = new bootstrap.Modal(document.getElementById('transferAgentModal'));
        agentModal.show();
    }

    // Function to select and add properties to owner transfer form
    function selectAndAddPropertiesToOwnerTransfer() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (!isPropertySelected()) {
            return alert('Please select at least one property.');
        }

        document.getElementById('transferOwnerPropertyIds').value = propertyIds.join(',');


        const ownerModal = new bootstrap.Modal(document.getElementById('transferOwnerModal'));
        ownerModal.show();
    }

    // Function to calculate square meters
    function sqftToSqm(sqft) {
        const sqm = sqft * 0.092903;
        return parseFloat(sqm.toFixed(2));
    }

    async function getNewReference(offeringType) {
        const prefix = (offeringType === "RR" || offeringType === "CR") ? "EARA-R-" : "EARA-S-";
        const url = `${API_BASE_URL}crm.item.list?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&order[id]=desc&select[]=ufCrm6ReferenceNumber`;

        try {
            const response = await fetch(url);
            const data = await response.json();
            const seriesItems = data.result.items.filter(item =>
                item.ufCrm6ReferenceNumber?.startsWith(prefix)
            );

            if (!seriesItems.length) {
                return `${prefix}0001`;
            }

            // Find the highest number in the existing references
            const highestNumber = seriesItems.reduce((max, item) => {
                const regex = new RegExp(`^${prefix}(\\d{4})$`);
                const match = item.ufCrm6ReferenceNumber.match(regex);
                if (match) {
                    const num = parseInt(match[1], 10);
                    return Math.max(max, num);
                }
                return max;
            }, 0);

            let nextNumber = String(highestNumber + 1).padStart(4, '0');
            let newReference = `${prefix}${nextNumber}`;

            // Check if the reference already exists using API call
            const checkUrl = `${API_BASE_URL}crm.item.list?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&filter[ufCrm6ReferenceNumber]=${newReference}&select[]=ufCrm6ReferenceNumber`;

            const checkResponse = await fetch(checkUrl);
            const checkData = await checkResponse.json();

            // If the reference exists, increment and check again
            while (checkData.result.items.length > 0) {
                nextNumber = String(parseInt(nextNumber, 10) + 1).padStart(4, '0');
                newReference = `${prefix}${nextNumber}`;

                // Make the check API call again with the new reference
                const retryCheckResponse = await fetch(`${API_BASE_URL}crm.item.list?entityTypeId=${LISTINGS_ENTITY_TYPE_ID}&filter[ufCrm6ReferenceNumber]=${newReference}&select[]=ufCrm6ReferenceNumber`);
                const retryCheckData = await retryCheckResponse.json();

                if (retryCheckData.result.items.length === 0) {
                    break;
                }
            }

            return newReference;
        } catch (error) {
            console.error('Error fetching reference:', error);
            return null;
        }
    }
</script>

</body>

</html>