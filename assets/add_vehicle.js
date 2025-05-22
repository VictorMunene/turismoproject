document.getElementById('addVehicleForm').addEventListener('submit', function(e) {
    const mileage = document.getElementById('mileage').value;
    const photosInput = document.getElementById('photos');
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    // Validate mileage
    if (mileage && mileage < 0) {
        e.preventDefault();
        alert('Mileage cannot be negative.');
        return;
    }

    // Validate photos
    if (photosInput.files.length > 0) {
        for (let file of photosInput.files) {
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                alert('Invalid file type. Only JPEG, PNG, and GIF are allowed.');
                return;
            }
            if (file.size > maxSize) {
                e.preventDefault();
                alert('File too large. Maximum size is 5MB.');
                return;
            }
        }
    }
});
