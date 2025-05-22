document.addEventListener('DOMContentLoaded', () => {
    const vehicleForm = document.querySelector('#addVehicleForm');

    vehicleForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(vehicleForm);
        const data = {
            make: formData.get('make'),
            model: formData.get('model'),
            year: formData.get('year'),
            price: formData.get('price'),
            description: formData.get('description')
        };

        // Client-side validation
        if (!data.make || !data.model) {
            alert('Make and Model are required.');
            return;
        }
        if (data.year < 1900 || data.year > new Date().getFullYear() + 1) {
            alert('Please enter a valid year.');
            return;
        }
        if (data.price < 0) {
            alert('Price cannot be negative.');
            return;
        }

        // Send data to backend
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        })
        .then(response => response.text())
        .then(text => {
            if (text.includes('Failed to add vehicle') || text.includes('Please fill all required fields')) {
                const errorDiv = document.createElement('p');
                errorDiv.className = 'error-message';
                errorDiv.textContent = text;
                vehicleForm.insertBefore(errorDiv, vehicleForm.querySelector('button'));
            } else {
                const successDiv = document.createElement('p');
                successDiv.className = 'success-message';
                successDiv.textContent = 'Vehicle added successfully!';
                vehicleForm.insertBefore(successDiv, vehicleForm.querySelector('button'));
                vehicleForm.reset();
            }
        })
        .catch(error => console.error('Error:', error));
    });
});