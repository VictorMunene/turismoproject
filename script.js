// Fetch cars from the Node.js server
document.addEventListener('DOMContentLoaded', () => {
    fetch('http://localhost:3000/cars')
        .then(response => response.json())
        .then(data => {
            console.log('Cars:', data);
            // Update the UI with the car data (e.g., populate the vehicle grid)
        })
        .catch(error => console.error('Error fetching cars:', error));
});
