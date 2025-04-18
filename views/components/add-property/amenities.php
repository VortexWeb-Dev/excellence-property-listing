<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-6">Amenities</h2>

    <div class="mb-4">
        <div class="relative">
            <input
                type="text"
                id="amenitySearch"
                placeholder="Search amenities..."
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                oninput="filterAmenities()">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="my-4 flex justify-between gap-6">
        <div class="w-full md:w-1/2 border rounded-lg p-4" style="max-height: 20rem; overflow-y: auto;">
            <label class="block text-sm font-medium mb-2">Available Amenities</label>
            <ul id="availableAmenities" class="list-none p-0 space-y-2">
                <!-- Available amenities will be displayed here -->
            </ul>
            <div id="noSearchResults" class="hidden text-gray-500 text-center py-4">No matching amenities found</div>
        </div>

        <div class="w-full md:w-1/2 border rounded-lg p-4" style="max-height: 20rem; overflow-y: auto;">
            <label class="block text-sm font-medium mb-2">Selected Amenities</label>
            <ul id="selectedAmenities" class="list-none p-0 space-y-2">
                <!-- Selected amenities will be displayed here -->
            </ul>
        </div>
    </div>

    <input type="hidden" name="amenities" id="amenitiesInput">
</div>

<script>
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

    amenities.sort((a, b) => a.label.localeCompare(b.label));

    let selectedAmenities = [];
    let filteredAmenities = [...amenities];

    function renderAmenities() {
        const availableAmenitiesContainer = document.getElementById("availableAmenities");
        const noSearchResults = document.getElementById("noSearchResults");
        availableAmenitiesContainer.innerHTML = "";

        // Filter out already selected amenities
        const availableFiltered = filteredAmenities.filter(
            amenity => !selectedAmenities.some(a => a.id === amenity.id)
        );

        if (availableFiltered.length === 0) {
            noSearchResults.classList.remove("hidden");
        } else {
            noSearchResults.classList.add("hidden");

            availableFiltered.forEach(amenity => {
                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md", "cursor-pointer", "hover:bg-gray-200");
                li.textContent = amenity.label;
                li.dataset.id = amenity.id;

                li.onclick = () => selectAmenity(amenity);

                availableAmenitiesContainer.appendChild(li);
            });
        }
    }

    function filterAmenities() {
        const searchTerm = document.getElementById("amenitySearch").value.toLowerCase();

        if (searchTerm === "") {
            filteredAmenities = [...amenities];
        } else {
            filteredAmenities = amenities.filter(amenity =>
                amenity.label.toLowerCase().includes(searchTerm) ||
                amenity.id.toLowerCase().includes(searchTerm)
            );
        }

        renderAmenities();
    }

    function selectAmenity(amenity) {
        if (selectedAmenities.some(a => a.id === amenity.id)) {
            alert("Amenity is already selected.");
            return;
        }

        selectedAmenities.push(amenity);
        updateSelectedAmenities();
        renderAmenities();
        updateAmenitiesInput();
    }

    function updateSelectedAmenities() {
        const selectedAmenitiesContainer = document.getElementById("selectedAmenities");
        selectedAmenitiesContainer.innerHTML = "";

        selectedAmenities.forEach(amenity => {
            const li = document.createElement("li");
            li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md");

            li.innerHTML = `
                ${amenity.label}
                <button type="button" class="text-red-500 hover:text-red-700" onclick="removeAmenity('${amenity.id}')">×</button>
            `;

            selectedAmenitiesContainer.appendChild(li);
        });
    }

    function getAmenityName(id) {
        const amenity = amenities.find(a => a.id === id);
        return amenity ? amenity.label : '';
    }

    function getAmenityId(name) {
        const amenity = amenities.find(a => a.label === name);
        return amenity ? amenity.id : '';
    }

    function removeAmenity(amenityId) {
        selectedAmenities = selectedAmenities.filter(a => a.id !== amenityId);
        updateSelectedAmenities();
        renderAmenities();
        updateAmenitiesInput();
    }

    function updateAmenitiesInput() {
        const selectedIds = selectedAmenities.map(amenity => amenity.id);
        document.getElementById("amenitiesInput").value = JSON.stringify(selectedIds);
    }

    // Initialize the component
    renderAmenities();
</script>